<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Liberu\Cms\Api\Filament\ApiTokenResource;
use Liberu\Cms\Api\Filament\Pages\ListApiTokens;
use Liberu\Cms\Users\Access\SyncPermissions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Sign a user into the panel holding the given CMS permissions, wired to their
 * personal team so Filament tenancy and Spatie's team-scoped permissions resolve
 * against the same tenant. Returns the team.
 *
 * @param  array<int, string>  $permissions
 */
function actingAsApiTokenAdmin(array $permissions = ['api-tokens.manage']): Team
{
    $user = User::factory()->create();
    $team = $user->createPersonalTeam();

    setPermissionsTeamId($team->id);
    app(SyncPermissions::class)();

    if ($permissions !== []) {
        $role = Role::create(['name' => 'cms-admin', 'team_id' => $team->id, 'guard_name' => 'web']);
        $role->givePermissionTo($permissions);
        $user->syncRoles([$role]);
    }

    test()->actingAs($user);
    setPermissionsTeamId($team->id);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($team);

    return $team;
}

it('denies the token surface to a user without the permission', function (): void {
    actingAsApiTokenAdmin([]);

    expect(ApiTokenResource::canViewAny())->toBeFalse();
});

it('grants the token surface to a user holding api-tokens.manage', function (): void {
    actingAsApiTokenAdmin();

    expect(ApiTokenResource::canViewAny())->toBeTrue();

    Livewire::test(ListApiTokens::class)->assertSuccessful();
});

it('lists only the current tenant tokens', function (): void {
    $team = actingAsApiTokenAdmin();
    $mine = $team->createToken('mine', ['content:read'])->accessToken;

    $other = Team::factory()->create();
    $theirs = $other->createToken('theirs', ['content:read'])->accessToken;

    Livewire::test(ListApiTokens::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});

it('mints a token through the header action and reveals it once', function (): void {
    $team = actingAsApiTokenAdmin();

    Livewire::test(ListApiTokens::class)
        ->callAction('create', ['name' => 'ci', 'write' => true])
        ->assertNotified('Delivery token created');

    $token = PersonalAccessToken::query()->where('name', 'ci')->first();

    expect($token)->not->toBeNull()
        ->and($token->tokenable_id)->toBe($team->id)
        ->and($token->abilities)->toContain('content:read')
        ->and($token->abilities)->toContain('content:write');
});

it('revokes a token so it can no longer authenticate the API', function (): void {
    $team = actingAsApiTokenAdmin();
    $new = $team->createToken('revoke-me', ['content:read']);
    $plain = $new->plainTextToken;

    $this->withHeaders(['Authorization' => 'Bearer '.$plain])
        ->getJson('/api/v1/pages')
        ->assertOk();

    Livewire::test(ListApiTokens::class)
        ->callTableAction('delete', $new->accessToken)
        ->assertHasNoTableActionErrors();

    $this->assertModelMissing($new->accessToken);

    // Drop the guard memoised from the first request so the token is re-resolved.
    $this->app['auth']->forgetGuards();

    $this->withHeaders(['Authorization' => 'Bearer '.$plain])
        ->getJson('/api/v1/pages')
        ->assertUnauthorized();
});

it('handles token issuance when the panel has no API-capable tenant', function (): void {
    actingAsApiTokenAdmin();
    Filament::setTenant(null);

    $method = new ReflectionMethod(ListApiTokens::class, 'getHeaderActions');
    $method->setAccessible(true);
    $action = $method->invoke(app(ListApiTokens::class))[0];

    $action->getActionFunction()(['name' => 'orphan', 'write' => false]);

    expect(PersonalAccessToken::query()->where('name', 'orphan')->exists())->toBeFalse();
});
