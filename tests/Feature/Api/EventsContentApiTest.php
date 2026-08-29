<?php

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\EventsContent\Models\Event;

uses(RefreshDatabase::class);

it('exposes only published event content through the API', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read'], 'sanctum');
    Event::query()->create(['key' => 'draft', 'title' => 'Draft', 'starts_at' => now()->addDay(), 'ends_at' => now()->addDays(2), 'status' => 'draft', 'team_id' => $team->id]);
    Event::query()->create(['key' => 'public', 'title' => 'Public Event', 'starts_at' => now()->addDay(), 'ends_at' => now()->addDays(2), 'status' => 'published', 'team_id' => $team->id]);

    $this->getJson('/api/v1/cms/events-content')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.key', 'public');
    $this->getJson('/api/v1/cms/events-content/draft')->assertNotFound();
});
