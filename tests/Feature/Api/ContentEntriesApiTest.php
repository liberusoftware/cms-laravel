<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->teamA = Team::factory()->create();
    $this->teamB = Team::factory()->create();
    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');

    $this->type = ContentType::factory()->create(['key' => 'portfolio']);
});

it('returns published entries of a given type', function (): void {
    ContentEntry::factory()->published()->create(['team_id' => $this->teamA->id, 'content_type_id' => $this->type->id, 'slug' => 'live-item']);
    ContentEntry::factory()->create(['team_id' => $this->teamA->id, 'content_type_id' => $this->type->id, 'slug' => 'draft-item']);

    $this->getJson('/api/v1/content/portfolio')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.slug', 'live-item')
        ->assertJsonMissing(['slug' => 'draft-item']);
});

it('fetches a single entry by slug with its typed fields', function (): void {
    ContentEntry::factory()->published()->create([
        'team_id' => $this->teamA->id,
        'content_type_id' => $this->type->id,
        'slug' => 'case-study',
        'data' => ['summary' => 'A great project', 'year' => 2026],
    ]);

    $this->getJson('/api/v1/content/portfolio/case-study')
        ->assertOk()
        ->assertJsonPath('data.type', 'portfolio')
        ->assertJsonPath('data.fields.summary', 'A great project')
        ->assertJsonPath('data.fields.year', 2026);
});

it('returns 404 for a draft entry, an unknown slug, and a mismatched type', function (): void {
    ContentEntry::factory()->create(['team_id' => $this->teamA->id, 'content_type_id' => $this->type->id, 'slug' => 'hidden']);
    $other = ContentType::factory()->create(['key' => 'product']);
    ContentEntry::factory()->published()->create(['team_id' => $this->teamA->id, 'content_type_id' => $other->id, 'slug' => 'a-product']);

    $this->getJson('/api/v1/content/portfolio/hidden')->assertNotFound();
    $this->getJson('/api/v1/content/portfolio/ghost')->assertNotFound();
    $this->getJson('/api/v1/content/portfolio/a-product')->assertNotFound();
});

it('does not leak another tenant\'s entry', function (): void {
    ContentEntry::factory()->published()->create(['team_id' => $this->teamB->id, 'content_type_id' => $this->type->id, 'slug' => 'team-b-entry']);

    $this->getJson('/api/v1/content/portfolio/team-b-entry')->assertNotFound();
    $this->getJson('/api/v1/content/portfolio')->assertOk()->assertJsonCount(0, 'data');
});
