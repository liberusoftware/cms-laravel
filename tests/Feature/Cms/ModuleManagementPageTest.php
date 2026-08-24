<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Admin\Filament\Pages\ModuleManagement;
use Liberu\Cms\Contracts\Module\ModuleManagerInterface;
use Liberu\Cms\Users\Access\SyncPermissions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Sign a user into the panel holding the given CMS permissions, wired to a
 * personal team so both Filament tenancy and Spatie's team-scoped permissions
 * resolve against the same tenant.
 *
 * @param  array<int, string>  $permissions
 */
function actingAsModuleAdmin(array $permissions = ['modules.view', 'modules.manage']): User
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

    return $user;
}

it('grants access to a user holding modules.view', function (): void {
    actingAsModuleAdmin(['modules.view']);

    expect(ModuleManagement::canAccess())->toBeTrue();
});

it('denies access to a user without the modules permission', function (): void {
    actingAsModuleAdmin([]);

    expect(ModuleManagement::canAccess())->toBeFalse();
});

it('renders the page listing the registered modules', function (): void {
    actingAsModuleAdmin();

    Livewire::test(ModuleManagement::class)
        ->assertSuccessful()
        ->assertSee('Admin')
        ->assertSee('Pages');
});

it('enables a disabled module', function (): void {
    actingAsModuleAdmin();
    app(ModuleManagerInterface::class)->disable('hello');

    Livewire::test(ModuleManagement::class)
        ->call('enable', 'hello');

    expect(app(ModuleManagerInterface::class)->isEnabled('hello'))->toBeTrue();
});

it('disables an enabled module', function (): void {
    actingAsModuleAdmin();

    Livewire::test(ModuleManagement::class)
        ->call('disable', 'hello');

    expect(app(ModuleManagerInterface::class)->isEnabled('hello'))->toBeFalse();
});

it('forbids managing modules without the modules.manage permission', function (): void {
    actingAsModuleAdmin(['modules.view']);

    Livewire::test(ModuleManagement::class)
        ->call('disable', 'hello')
        ->assertForbidden();

    expect(app(ModuleManagerInterface::class)->isEnabled('hello'))->toBeTrue();
});

it('refuses to disable a module other enabled modules depend on', function (): void {
    actingAsModuleAdmin();

    // Pages depends on Media, so Media cannot be disabled while Pages is enabled.
    Livewire::test(ModuleManagement::class)
        ->call('disable', 'media');

    expect(app(ModuleManagerInterface::class)->isEnabled('media'))->toBeTrue();
});

it('reports a rejected module enable operation', function (): void {
    actingAsModuleAdmin();

    app(ModuleManagerInterface::class)->disable('pages');
    app(ModuleManagerInterface::class)->disable('posts');
    app(ModuleManagerInterface::class)->disable('media');

    Livewire::test(ModuleManagement::class)
        ->call('enable', 'pages')
        ->assertNotified('That change was rejected.');
});

it('falls back to the default navigation group for invalid configuration', function (): void {
    actingAsModuleAdmin();
    config()->set('cms-admin.navigation_group', ['invalid']);

    expect(ModuleManagement::getNavigationGroup())->toBe('CMS');
});
