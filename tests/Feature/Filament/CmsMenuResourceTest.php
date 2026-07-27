<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Menus\Filament\MenuItemResource;
use Liberu\Cms\Menus\Filament\MenuResource;
use Liberu\Cms\Menus\Filament\Pages\ListMenuItems;
use Liberu\Cms\Menus\Filament\Pages\ListMenus;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\Menus\Models\MenuItem;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
    grantCmsPermissions($this->user, $this->team, ['menus.view', 'menus.create', 'menus.update', 'menus.delete']);

    $panel = Filament::getPanel('app');
    Filament::setCurrentPanel($panel);

    // Livewire::test() does not run Panel::boot(), so register the tenancy scope
    // and creation observer the way a real request would.
    foreach ([MenuResource::class, MenuItemResource::class] as $resource) {
        $resource::registerTenancyModelGlobalScope($panel);
        $resource::observeTenancyModelCreation($panel);
    }

    Filament::setTenant($this->team);
});

it('renders the menus list', function (): void {
    Livewire::test(ListMenus::class)->assertSuccessful();
});

it('lists menu records', function (): void {
    $menus = Menu::factory()->count(3)->create();

    Livewire::test(ListMenus::class)->assertCanSeeTableRecords($menus);
});

it('creates a menu through the modal', function (): void {
    Livewire::test(ListMenus::class)
        ->callAction('create', [
            'name' => 'Primary Navigation',
            'location' => 'header',
        ]);

    $this->assertDatabaseHas('cms_menus', [
        'name' => 'Primary Navigation',
        'location' => 'header',
    ]);
});

it('deletes a menu through the row action', function (): void {
    $menu = Menu::factory()->create();

    Livewire::test(ListMenus::class)->callTableAction('delete', $menu);

    $this->assertModelMissing($menu);
});

it('renders the menu items list', function (): void {
    Livewire::test(ListMenuItems::class)->assertSuccessful();
});

it('creates a menu item attached to a menu', function (): void {
    $menu = Menu::factory()->create();

    Livewire::test(ListMenuItems::class)
        ->callAction('create', [
            'menu_id' => $menu->id,
            'label' => 'About Us',
            'url' => '/about',
            'sort' => 1,
        ]);

    $this->assertDatabaseHas('cms_menu_items', [
        'menu_id' => $menu->id,
        'label' => 'About Us',
        'url' => '/about',
    ]);
});

it('lists menu item records', function (): void {
    $menu = Menu::factory()->create();
    $items = MenuItem::factory()->count(2)->for($menu)->create();

    Livewire::test(ListMenuItems::class)->assertCanSeeTableRecords($items);
});

it('scopes menus to the current tenant', function (): void {
    Filament::setTenant($this->team);
    $mine = Menu::factory()->create();

    $otherTeam = Team::factory()->create(['user_id' => $this->user->id]);
    Filament::setTenant($otherTeam);
    $theirs = Menu::factory()->create();

    Filament::setTenant($this->team);

    expect($mine->team_id)->toBe($this->team->id)
        ->and($theirs->team_id)->toBe($otherTeam->id);

    Livewire::test(ListMenus::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});
