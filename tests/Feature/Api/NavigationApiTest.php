<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Menus\Models\Menu;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    Sanctum::actingAs($this->team, ['content:read', 'content:write'], 'sanctum');
});

it('creates a variant menu and a typed navigation item through the API', function (): void {
    $menu = $this->postJson('/api/v1/cms/navigation', [
        'name' => 'Mobile',
        'location' => 'header',
        'variant' => 'mobile',
    ])->assertCreated()->json('data');

    $this->postJson("/api/v1/cms/navigation/{$menu['id']}/items", [
        'label' => 'Article',
        'link_type' => 'content',
        'content_id' => 'article-01',
        'url' => '/article',
    ])->assertCreated()->assertJsonPath('data.link_type', 'content');

    $this->assertDatabaseHas('cms_menus', ['location' => 'header', 'variant' => 'mobile']);
});

it('does not expose a navigation menu across tenants', function (): void {
    $other = Team::withoutEvents(fn (): Team => Team::factory()->create());
    Menu::factory()->create(['team_id' => $other->id, 'location' => 'footer']);

    $this->getJson('/api/v1/cms/navigation')->assertOk()->assertJsonCount(0, 'data');
});
