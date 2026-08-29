<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\Menus\Models\MenuItem;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders a recursive active navigation tree through the package alias', function (): void {
    $menu = Menu::factory()->create(['location' => 'header']);
    $parent = MenuItem::factory()->for($menu)->create(['label' => 'Docs', 'url' => '/docs']);
    MenuItem::factory()->for($menu)->create(['parent_id' => $parent->id, 'label' => 'Install', 'url' => '/docs/install']);

    Livewire::test('module-cms-navigation.menu')
        ->set('path', '/docs/install')
        ->assertSee('Docs')
        ->assertSee('Install')
        ->assertSee('aria-current="page"', false);
});
