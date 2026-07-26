<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Api\Support\PreviewLink;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Pages\Models\Page;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->teamA = Team::factory()->create();
    $this->teamB = Team::factory()->create();
    // Factory default status is Draft.
    $this->draft = Page::factory()->create(['team_id' => $this->teamA->id, 'slug' => 'secret-draft', 'title' => 'Secret Draft']);
    $this->links = app(PreviewLink::class);
});

it('previews a draft page through a valid signed link', function (): void {
    $url = $this->links->for('pages', $this->draft->id, $this->teamA->id);

    $this->getJson($url)
        ->assertOk()
        ->assertJsonPath('data.id', $this->draft->id)
        ->assertJsonPath('data.slug', 'secret-draft');
});

it('rejects an expired preview link', function (): void {
    $expired = $this->links->for('pages', $this->draft->id, $this->teamA->id, -5);

    $this->getJson($expired)->assertForbidden();
});

it('rejects a tampered preview link', function (): void {
    $url = $this->links->for('pages', $this->draft->id, $this->teamA->id);

    $this->getJson($url.'&injected=1')->assertForbidden();
});

it('does not reveal an item through a link minted for another tenant', function (): void {
    // A validly signed link, but naming teamB — the tenant scope must still hide teamA's page.
    $crossTenant = $this->links->for('pages', $this->draft->id, $this->teamB->id);

    $this->getJson($crossTenant)->assertNotFound();
});

it('returns 404 for an unknown preview type', function (): void {
    $url = $this->links->for('gadgets', 1, $this->teamA->id);

    $this->getJson($url)->assertNotFound();
});

it('still hides the draft from the published read endpoint', function (): void {
    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');

    // The published API endpoint 404s the draft...
    $this->getJson('/api/v1/pages/secret-draft')->assertNotFound();
});

it('previews a draft content entry through a valid signed link', function (): void {
    $type = ContentType::factory()->create(['key' => 'portfolio']);
    $entry = ContentEntry::factory()->create([
        'team_id' => $this->teamA->id,
        'content_type_id' => $type->id,
        'slug' => 'draft-entry',
    ]);

    $url = $this->links->for('content-entries', $entry->id, $this->teamA->id);

    $this->getJson($url)
        ->assertOk()
        ->assertJsonPath('data.type', 'portfolio')
        ->assertJsonPath('data.slug', 'draft-entry');
});

it('mints a preview link from the command', function (): void {
    $this->artisan('cms-api:preview-link', ['type' => 'pages', 'id' => $this->draft->id])
        ->expectsOutputToContain('api/v1/preview/pages/'.$this->draft->id)
        ->assertSuccessful();
});

it('fails the command for an unknown type', function (): void {
    $this->artisan('cms-api:preview-link', ['type' => 'gadgets', 'id' => 1])
        ->assertFailed();
});

it('fails the command for a missing item', function (): void {
    $this->artisan('cms-api:preview-link', ['type' => 'pages', 'id' => 999999])
        ->assertFailed();
});
