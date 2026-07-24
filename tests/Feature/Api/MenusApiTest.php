<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\Menus\Models\MenuItem;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->teamA = Team::factory()->create();
    $this->teamB = Team::factory()->create();
    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');
});

it('fetches a menu by location with its ordered, nested item tree', function (): void {
    $menu = Menu::factory()->create(['team_id' => $this->teamA->id, 'name' => 'Main', 'location' => 'header']);

    $home = MenuItem::factory()->create(['team_id' => $this->teamA->id, 'menu_id' => $menu->id, 'label' => 'Home', 'url' => '/', 'sort' => 0]);
    $about = MenuItem::factory()->create(['team_id' => $this->teamA->id, 'menu_id' => $menu->id, 'label' => 'About', 'url' => '/about', 'sort' => 1]);
    MenuItem::factory()->create(['team_id' => $this->teamA->id, 'menu_id' => $menu->id, 'parent_id' => $about->id, 'label' => 'Team', 'url' => '/about/team', 'sort' => 0]);

    $this->getJson('/api/v1/menus/header')
        ->assertOk()
        ->assertJsonPath('data.location', 'header')
        ->assertJsonPath('data.items.0.label', 'Home')
        ->assertJsonPath('data.items.1.label', 'About')
        ->assertJsonPath('data.items.1.children.0.label', 'Team');
});

it('returns 404 for an unknown location', function (): void {
    $this->getJson('/api/v1/menus/footer')->assertNotFound();
});

it('does not leak another tenant\'s menu', function (): void {
    Menu::factory()->create(['team_id' => $this->teamB->id, 'location' => 'sidebar']);

    $this->getJson('/api/v1/menus/sidebar')->assertNotFound();
});
