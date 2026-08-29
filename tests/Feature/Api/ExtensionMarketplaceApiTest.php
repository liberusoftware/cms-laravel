<?php

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionListing;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionPublisher;

uses(RefreshDatabase::class);

it('exposes only approved extension listings and conceals pending details', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read'], 'sanctum');
    $publisher = ExtensionPublisher::query()->create(['key' => 'p', 'name' => 'Publisher', 'team_id' => $team->id]);
    ExtensionListing::query()->create(['publisher_id' => $publisher->id, 'key' => 'pending', 'name' => 'Pending', 'status' => 'published', 'security_status' => 'pending', 'team_id' => $team->id]);
    ExtensionListing::query()->create(['publisher_id' => $publisher->id, 'key' => 'approved', 'name' => 'Approved', 'status' => 'published', 'security_status' => 'approved', 'team_id' => $team->id]);

    $this->getJson('/api/v1/cms/extension-marketplace')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.key', 'approved');
    $this->getJson('/api/v1/cms/extension-marketplace/pending')->assertNotFound();
});
