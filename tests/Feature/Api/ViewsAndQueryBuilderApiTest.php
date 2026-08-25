<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\ViewsAndQueryBuilder\Models\ViewDefinition;

uses(RefreshDatabase::class);

it('exposes only published views and executes their safe listing definition', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read'], 'sanctum');

    $collection = Collection::create(['name' => 'Articles', 'team_id' => $team->id]);
    $collection->items()->create(['title' => 'Visible', 'status' => 'published', 'published_at' => now(), 'team_id' => $team->id]);
    $collection->items()->create(['title' => 'Hidden', 'status' => 'draft', 'team_id' => $team->id]);
    ViewDefinition::create([
        'name' => 'Published articles',
        'source' => 'collection_items',
        'definition' => ['fields' => ['title', 'status'], 'filters' => [['field' => 'status', 'operator' => '=', 'value' => 'published']]],
        'status' => 'published',
        'published_at' => now(),
        'team_id' => $team->id,
    ]);
    ViewDefinition::create(['name' => 'Draft view', 'source' => 'collection_items', 'definition' => ['fields' => ['title']], 'team_id' => $team->id]);

    $this->getJson('/api/v1/cms/views-and-query-builder')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.type', 'cms-view-definition')
        ->assertJsonPath('data.0.slug', 'published-articles');

    $this->getJson('/api/v1/cms/views-and-query-builder/published-articles/execute')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.type', 'cms-listing-record')
        ->assertJsonPath('data.0.attributes.title', 'Visible');

    $this->getJson('/api/v1/cms/views-and-query-builder/draft-view')->assertNotFound();
});
