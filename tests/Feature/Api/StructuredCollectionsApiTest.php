<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Team;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Collections\Models\Collection;

uses(RefreshDatabase::class);

it('exposes the canonical structured collections API contract', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read'], 'sanctum');
    $collection = Collection::create(['name' => 'FAQs', 'slug' => 'faqs', 'type' => 'faq', 'team_id' => $team->id]);
    $collection->items()->create(['title' => 'What?', 'status' => 'published', 'published_at' => now(), 'team_id' => $team->id]);

    $this->getJson('/api/v1/cms/structured-collections/faqs')
        ->assertOk()
        ->assertJsonPath('data.type', 'cms-structured-collections')
        ->assertJsonPath('data.slug', 'faqs');

    $this->getJson('/api/v1/cms/structured-collections/faqs/records')
        ->assertOk()
        ->assertJsonPath('data.0.type', 'cms-structured-collection-record')
        ->assertJsonPath('data.0.title', 'What?');
});
