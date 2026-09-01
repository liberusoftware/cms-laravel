<?php

declare(strict_types=1);

namespace Liberu\Cms\AuditAndHistoryFilament\Resources;

use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Audit\Models\AuditLog;
use Liberu\Cms\AuditAndHistoryFilament\Resources\Pages\ListAuditLogs;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use UnitEnum;

final class AuditLogResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static ?string $slug = 'cms-audit-logs';

    protected static ?string $navigationLabel = 'Audit log';

    protected static bool $isScopedToTenant = false;

    protected static function cmsPermissionKey(): string
    {
        return 'audit';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->label('When')->dateTime()->sortable(),
            TextColumn::make('action')->badge()->sortable(),
            TextColumn::make('actor_label')->label('Actor')->placeholder('—')->searchable(),
            TextColumn::make('subject_type')->label('Subject')->formatStateUsing(fn (AuditLog $record): string => $record->subject_type === null ? '—' : $record->subject_type.($record->subject_id !== null ? " #{$record->subject_id}" : ''))->searchable(['subject_type', 'subject_id']),
            TextColumn::make('ip_address')->label('IP')->toggleable(),
        ])->defaultSort('created_at', 'desc')->filters([
            SelectFilter::make('action')->options([
                'auth.login' => 'Login', 'auth.logout' => 'Logout', 'auth.failed' => 'Login failed',
                'content.published' => 'Content published', 'content.state_changed' => 'Content state changed',
            ]),
            Filter::make('created_at')->query(function (Builder $query, array $data): Builder {
                if (is_string($data['from'] ?? null) && $data['from'] !== '') {
                    $query->whereDate('created_at', '>=', $data['from']);
                }
                if (is_string($data['until'] ?? null) && $data['until'] !== '') {
                    $query->whereDate('created_at', '<=', $data['until']);
                }

                return $query;
            }),
        ]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListAuditLogs::route('/')];
    }
}
