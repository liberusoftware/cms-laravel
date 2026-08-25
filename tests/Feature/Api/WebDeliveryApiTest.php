<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\WebDelivery\Models\DeliveryRoute;

uses(RefreshDatabase::class);

it('resolves delivery routes and exposes metadata through the API', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read', 'content:write'], 'sanctum');
    DeliveryRoute::create(['path' => '/home', 'body' => 'Welcome', 'metadata' => ['title' => 'Home'], 'cache_tags' => ['page:1'], 'status' => 'published', 'team_id' => $team->id]);

    $this->getJson('/api/v1/cms/web-delivery/resolve?path=%2Fhome')
        ->assertOk()
        ->assertJsonPath('data.body', 'Welcome')
        ->assertJsonPath('data.metadata.title', 'Home')
        ->assertJsonPath('data.cache_tags.0', 'page:1');

    $this->getJson('/api/v1/cms/web-delivery/resolve?path=%2Fmissing')->assertNotFound();
});
