<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    // Factory default schema: summary (text, required), year (number, optional).
    $this->type = ContentType::factory()->create(['key' => 'portfolio']);

    Sanctum::actingAs($this->team, ['content:read', 'content:write'], 'sanctum');
});

it('creates an entry when data satisfies the schema', function (): void {
    $this->postJson('/api/v1/content-entries', [
        'content_type_id' => $this->type->id,
        'title' => 'Valid',
        'data' => ['summary' => 'A thing', 'year' => 2026],
    ])->assertCreated();

    $this->assertDatabaseHas('cms_content_entries', ['slug' => 'valid', 'team_id' => $this->team->id]);
});

it('rejects a create missing a required field, naming the field', function (): void {
    $this->postJson('/api/v1/content-entries', [
        'content_type_id' => $this->type->id,
        'title' => 'No summary',
        'data' => ['year' => 2026],
    ])->assertStatus(422)->assertJsonValidationErrors('data.summary');
});

it('rejects a create whose field has the wrong type', function (): void {
    $this->postJson('/api/v1/content-entries', [
        'content_type_id' => $this->type->id,
        'title' => 'Bad year',
        'data' => ['summary' => 'ok', 'year' => 'not-a-number'],
    ])->assertStatus(422)->assertJsonValidationErrors('data.year');
});

it('rejects a create carrying a field not defined by the schema', function (): void {
    $this->postJson('/api/v1/content-entries', [
        'content_type_id' => $this->type->id,
        'title' => 'Rogue field',
        'data' => ['summary' => 'ok', 'rogue' => 'nope'],
    ])->assertStatus(422)->assertJsonValidationErrors('data.rogue');
});

it('validates update data against the persisted type, not a spoofed content_type_id', function (): void {
    $other = ContentType::factory()->create([
        'key' => 'press',
        'fields' => [['name' => 'headline', 'label' => 'Headline', 'type' => 'text', 'required' => true]],
    ]);
    $entry = ContentEntry::factory()->create([
        'team_id' => $this->team->id,
        'content_type_id' => $this->type->id,
        'slug' => 'mine',
        'data' => ['summary' => 'original'],
    ]);

    // Data valid for the spoofed "press" type but not for the entry's real "portfolio" type.
    $this->putJson("/api/v1/content-entries/{$entry->id}", [
        'content_type_id' => $other->id,
        'data' => ['headline' => 'Breaking'],
    ])->assertStatus(422)->assertJsonValidationErrors('data.summary');
});

it('accepts a valid update that replaces the data payload', function (): void {
    $entry = ContentEntry::factory()->create([
        'team_id' => $this->team->id,
        'content_type_id' => $this->type->id,
        'slug' => 'mine',
        'data' => ['summary' => 'original'],
    ]);

    $this->putJson("/api/v1/content-entries/{$entry->id}", [
        'data' => ['summary' => 'updated', 'year' => 1999],
    ])->assertOk()->assertJsonPath('data.fields.summary', 'updated');
});
