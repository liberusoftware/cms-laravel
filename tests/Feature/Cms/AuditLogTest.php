<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Audit\AuditLogger;
use Liberu\Cms\Audit\Models\AuditLog;
use Liberu\Cms\AuditAndHistoryFilament\Resources\AuditLogResource;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Contracts\Events\Content\ContentPublished;
use Liberu\Cms\Contracts\Events\Content\ContentStateChanged;
use Liberu\Cms\Contracts\Events\EventBusInterface;

uses(RefreshDatabase::class);

it('records a successful login with the actor', function (): void {
    $user = User::factory()->create(['email' => 'editor@example.com']);

    Auth::login($user);

    $log = AuditLog::query()->where('action', 'auth.login')->sole();
    expect($log->actor_id)->toBe((string) $user->id)
        ->and($log->actor_label)->toBe('editor@example.com');
});

it('records a failed login attempt with the attempted email but no actor', function (): void {
    Auth::attempt(['email' => 'ghost@example.com', 'password' => 'wrong-password']);

    $log = AuditLog::query()->where('action', 'auth.failed')->sole();
    expect($log->actor_id)->toBeNull()
        ->and($log->actor_label)->toBe('ghost@example.com');
});

it('records a logout', function (): void {
    $user = User::factory()->create();
    Auth::login($user);
    Auth::logout();

    expect(AuditLog::query()->where('action', 'auth.logout')->count())->toBe(1);
});

it('records a content publish with its subject', function (): void {
    app(EventBusInterface::class)->dispatch(new ContentPublished('page', 42));

    $log = AuditLog::query()->where('action', 'content.published')->sole();
    expect($log->subject_type)->toBe('page')
        ->and($log->subject_id)->toBe('42');
});

it('records a workflow state change with from/to metadata', function (): void {
    app(EventBusInterface::class)->dispatch(
        new ContentStateChanged('post', 7, WorkflowState::Draft, WorkflowState::Published),
    );

    $log = AuditLog::query()->where('action', 'content.state_changed')->sole();
    expect($log->subject_id)->toBe('7')
        ->and($log->metadata)->toEqualCanonicalizing(['from' => 'draft', 'to' => 'published']);
});

it('produces exactly one row per event', function (): void {
    app(EventBusInterface::class)->dispatch(new ContentPublished('page', 1));

    expect(AuditLog::query()->count())->toBe(1);
});

it('rejects invalid audit action identifiers', function (): void {
    $logger = app(AuditLogger::class);

    expect(fn () => $logger->record(' '))->toThrow(ValidationException::class)
        ->and(fn () => $logger->record(str_repeat('x', 256)))->toThrow(ValidationException::class);
});

it('refuses to update an audit record', function (): void {
    $log = AuditLog::query()->create(['action' => 'auth.login']);

    expect(fn () => $log->update(['action' => 'tampered']))
        ->toThrow(RuntimeException::class);
});

it('refuses to delete an audit record', function (): void {
    $log = AuditLog::query()->create(['action' => 'auth.login']);

    expect(fn () => $log->delete())->toThrow(RuntimeException::class);
});

it('gates the viewer behind the audit.view permission', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($team);
    setPermissionsTeamId($team->id);

    expect(AuditLogResource::canViewAny())->toBeFalse();

    grantCmsPermissions($user, $team, ['audit.view']);

    expect(AuditLogResource::canViewAny())->toBeTrue()
        ->and(AuditLogResource::canCreate())->toBeFalse()
        ->and(AuditLogResource::canDelete(new AuditLog))->toBeFalse();
});

it('builds a read-only audit table with its supported filters', function (): void {
    $table = AuditLogResource::table(
        Table::make(Mockery::mock(HasTable::class)),
    );

    expect($table->getColumns())->toHaveCount(5)
        ->and($table->getFilters())->toHaveCount(2)
        ->and(AuditLogResource::canCreate())->toBeFalse()
        ->and(AuditLogResource::canEdit(new AuditLog))->toBeFalse()
        ->and(AuditLogResource::canDeleteAny())->toBeFalse();

    $query = AuditLog::query();
    $dateFilter = array_values($table->getFilters())[1];
    $dateFilter->apply($query, ['from' => '2026-01-01', 'until' => '2026-12-31']);

    $dateExpression = match (DB::connection()->getDriverName()) {
        'pgsql' => '::date',
        'mysql' => 'date(',
        default => 'strftime',
    };

    expect($query)->toBeInstanceOf(Builder::class)
        ->and($query->toSql())->toContain($dateExpression)
        ->and($query->getBindings())->toBe(['2026-01-01', '2026-12-31']);

    $subjectColumn = $table->getColumns()['subject_type'];
    expect($subjectColumn->record(AuditLog::make())->formatState(null))->toBe('—');
});
