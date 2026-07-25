<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Pages\Models\Page;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->teamA = Team::factory()->create();
    $this->teamB = Team::factory()->create();
});

function asWriter(Team $team): void
{
    Sanctum::actingAs($team, ['content:read', 'content:write'], 'sanctum');
}

function asReader(Team $team): void
{
    Sanctum::actingAs($team, ['content:read'], 'sanctum');
}

it('rejects an unauthenticated write with 401', function (): void {
    $this->postJson('/api/v1/pages', ['title' => 'X'])->assertUnauthorized();
});

it('forbids a read-only token from writing with 403', function (): void {
    asReader($this->teamA);

    $this->postJson('/api/v1/pages', ['title' => 'X'])->assertForbidden();
});

it('creates a page as a draft, stamped with the token tenant', function (): void {
    asWriter($this->teamA);

    $response = $this->postJson('/api/v1/pages', [
        'title' => 'New Page',
        'content' => 'Body',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'New Page')
        ->assertJsonPath('data.slug', 'new-page');

    $this->assertDatabaseHas('cms_pages', [
        'slug' => 'new-page',
        'team_id' => $this->teamA->id,
        'status' => WorkflowState::Draft->value,
    ]);
});

it('creates a page as published via the workflow', function (): void {
    asWriter($this->teamA);

    $response = $this->postJson('/api/v1/pages', [
        'title' => 'Live Page',
        'status' => 'published',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.published_at', fn ($value) => $value !== null);

    $this->assertDatabaseHas('cms_pages', ['slug' => 'live-page', 'status' => WorkflowState::Published->value]);
});

it('validates the create payload', function (): void {
    asWriter($this->teamA);

    $this->postJson('/api/v1/pages', ['content' => 'no title'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('title');
});

it('updates a page', function (): void {
    asWriter($this->teamA);
    $page = Page::factory()->create(['team_id' => $this->teamA->id, 'title' => 'Old', 'slug' => 'old']);

    $this->putJson("/api/v1/pages/{$page->id}", ['title' => 'Updated'])
        ->assertOk()
        ->assertJsonPath('data.title', 'Updated');

    expect($page->refresh()->title)->toBe('Updated');
});

it('rejects an illegal status transition with 422', function (): void {
    asWriter($this->teamA);
    $page = Page::factory()->published()->create(['team_id' => $this->teamA->id, 'slug' => 'pub']);

    // Published -> Review is not an allowed transition.
    $this->putJson("/api/v1/pages/{$page->id}", ['status' => 'review'])
        ->assertStatus(422);
});

it('does not update or delete another tenant\'s page (404)', function (): void {
    asWriter($this->teamA);
    $other = Page::factory()->create(['team_id' => $this->teamB->id, 'slug' => 'theirs']);

    $this->putJson("/api/v1/pages/{$other->id}", ['title' => 'Hacked'])->assertNotFound();
    $this->deleteJson("/api/v1/pages/{$other->id}")->assertNotFound();

    expect($other->refresh()->title)->not->toBe('Hacked');
});

it('deletes a page', function (): void {
    asWriter($this->teamA);
    $page = Page::factory()->create(['team_id' => $this->teamA->id, 'slug' => 'bye']);

    $this->deleteJson("/api/v1/pages/{$page->id}")->assertNoContent();

    $this->assertDatabaseMissing('cms_pages', ['id' => $page->id]);
});
