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
    $this->type = ContentType::factory()->create(['key' => 'portfolio']);

    Sanctum::actingAs($this->teamA, ['content:read', 'content:write'], 'sanctum');
});

it('validates content_type_id on create', function (): void {
    $this->postJson('/api/v1/content-entries', ['title' => 'No type'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('content_type_id');

    $this->postJson('/api/v1/content-entries', ['title' => 'Bad type', 'content_type_id' => 99999])
        ->assertStatus(422)
        ->assertJsonValidationErrors('content_type_id');
});

it('creates a content entry with typed data, stamped with the tenant', function (): void {
    $response = $this->postJson('/api/v1/content-entries', [
        'content_type_id' => $this->type->id,
        'title' => 'My Item',
        'data' => ['summary' => 'A thing', 'year' => 2026],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.type', 'portfolio')
        ->assertJsonPath('data.fields.summary', 'A thing');

    $this->assertDatabaseHas('cms_content_entries', [
        'slug' => 'my-item',
        'team_id' => $this->teamA->id,
    ]);
});

it('updates and deletes an entry, isolating tenants', function (): void {
    $entry = ContentEntry::factory()->create(['team_id' => $this->teamA->id, 'content_type_id' => $this->type->id, 'slug' => 'mine']);
    $other = ContentEntry::factory()->create(['team_id' => $this->teamB->id, 'content_type_id' => $this->type->id, 'slug' => 'theirs']);

    $this->putJson("/api/v1/content-entries/{$entry->id}", ['title' => 'Renamed'])
        ->assertOk()
        ->assertJsonPath('data.title', 'Renamed');

    $this->putJson("/api/v1/content-entries/{$other->id}", ['title' => 'Nope'])->assertNotFound();

    $this->deleteJson("/api/v1/content-entries/{$entry->id}")->assertNoContent();
    $this->assertDatabaseMissing('cms_content_entries', ['id' => $entry->id]);
});
