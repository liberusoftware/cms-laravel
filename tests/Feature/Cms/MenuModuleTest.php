<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Menus\Contracts\MenuRepositoryInterface;
use Liberu\Cms\Menus\MenuBuilder;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\Menus\Models\MenuItem;
use Liberu\Cms\Menus\Services\MenuService;
use Liberu\Cms\Users\Access\SyncPermissions;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('resolves a menu by location', function (): void {
    Menu::factory()->create(['location' => 'footer', 'name' => 'Footer']);

    $menu = app(MenuRepositoryInterface::class)->forLocation('footer');

    expect($menu?->name)->toBe('Footer');
});

it('builds a nested, ordered menu tree', function (): void {
    $menu = Menu::factory()->create(['location' => 'header']);
    $about = MenuItem::factory()->for($menu)->create(['label' => 'About', 'sort' => 1]);
    MenuItem::factory()->for($menu)->create(['label' => 'Home', 'sort' => 0]);
    MenuItem::factory()->for($menu)->create(['label' => 'Team', 'parent_id' => $about->id]);

    $tree = app(MenuBuilder::class)->tree($menu);

    expect($tree)->toHaveCount(2)
        ->and($tree[0]->label)->toBe('Home')
        ->and($tree[1]->label)->toBe('About')
        ->and($tree[1]->children)->toHaveCount(1)
        ->and($tree[1]->children[0]->label)->toBe('Team');
});

it('hides items whose required permission the user lacks', function (): void {
    $user = User::factory()->create();
    $team = $user->createPersonalTeam();
    setPermissionsTeamId($team->id);
    app(SyncPermissions::class)();

    $menu = Menu::factory()->create(['location' => 'header']);
    MenuItem::factory()->for($menu)->create(['label' => 'Public', 'sort' => 0]);
    MenuItem::factory()->for($menu)->create(['label' => 'Admin', 'sort' => 1, 'permission' => 'users.view']);

    $this->actingAs($user);
    setPermissionsTeamId($team->id);

    $tree = app(MenuBuilder::class)->tree($menu);

    expect($tree)->toHaveCount(1)
        ->and($tree[0]->label)->toBe('Public');
});

it('shows a permission-gated item to a user who has the permission', function (): void {
    $user = User::factory()->create();
    $team = $user->createPersonalTeam();
    setPermissionsTeamId($team->id);
    app(SyncPermissions::class)();

    $role = Role::create(['name' => 'menu-role', 'team_id' => $team->id, 'guard_name' => 'web']);
    $role->givePermissionTo('users.view');
    $user->syncRoles([$role]);

    $menu = Menu::factory()->create(['location' => 'header']);
    MenuItem::factory()->for($menu)->create(['label' => 'Admin', 'permission' => 'users.view']);

    $this->actingAs($user);
    setPermissionsTeamId($team->id);

    $tree = app(MenuBuilder::class)->tree($menu);

    expect($tree)->toHaveCount(1)
        ->and($tree[0]->label)->toBe('Admin');
});

it('hides the whole subtree of a hidden parent', function (): void {
    $menu = Menu::factory()->create(['location' => 'header']);
    $secret = MenuItem::factory()->for($menu)->create(['label' => 'Secret', 'permission' => 'users.view']);
    MenuItem::factory()->for($menu)->create(['label' => 'Child', 'parent_id' => $secret->id]);

    // No authenticated user → can() is false → parent and child hidden.
    $tree = app(MenuBuilder::class)->tree($menu);

    expect($tree)->toBe([]);
});

it('supports menu variants and typed link validation through the service boundary', function (): void {
    $service = app(MenuService::class);
    $menu = $service->createMenu(['name' => 'Mobile', 'location' => 'header', 'variant' => 'mobile']);

    $item = $service->saveItem($menu, [
        'label' => 'Article',
        'link_type' => 'content',
        'content_id' => 'content-01',
        'url' => '/article',
    ]);

    expect(app(MenuRepositoryInterface::class)->forLocation('header', 'mobile')?->is($menu))->toBeTrue()
        ->and($item->link_type)->toBe('content');

    expect(fn () => $service->saveItem($menu, [
        'label' => 'Broken',
        'link_type' => 'system',
    ]))->toThrow(ValidationException::class);
});

it('marks active trails and applies path visibility rules', function (): void {
    $menu = Menu::factory()->create();
    MenuItem::factory()->for($menu)->create(['label' => 'Visible', 'url' => '/docs', 'visibility' => ['path_prefix' => '/docs']]);
    MenuItem::factory()->for($menu)->create(['label' => 'Hidden', 'url' => '/admin', 'visibility' => ['path_prefix' => '/admin']]);

    $tree = app(MenuBuilder::class)->tree($menu, '/docs/install');

    expect($tree)->toHaveCount(1)
        ->and($tree[0]->label)->toBe('Visible')
        ->and($tree[0]->active)->toBeFalse();
});
