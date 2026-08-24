<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Posts\Models\Post;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->teamA = Team::factory()->create();
    $this->teamB = Team::factory()->create();
});

it('rejects an unauthenticated search with 401', function (): void {
    $this->getJson('/api/v1/search?q=laravel')->assertUnauthorized();
});

it('rejects a missing or too-short query with 422', function (): void {
    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');

    $this->getJson('/api/v1/search')->assertStatus(422);
    $this->getJson('/api/v1/search?q=a')->assertStatus(422);
});

it('returns published matches across content types for the tenant', function (): void {
    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');

    Page::factory()->published()->create(['team_id' => $this->teamA->id, 'title' => 'Laravel Guide', 'slug' => 'laravel-guide', 'content' => 'x']);
    Post::factory()->published()->create(['team_id' => $this->teamA->id, 'title' => 'Laravel Tips', 'slug' => 'laravel-tips', 'content' => 'x']);
    Page::factory()->published()->create(['team_id' => $this->teamA->id, 'title' => 'Cooking', 'slug' => 'cooking', 'content' => 'food']);

    $response = $this->getJson('/api/v1/search?q=Laravel');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('meta.total', 2);

    $types = collect($response->json('data'))->pluck('type')->sort()->values()->all();
    expect($types)->toBe(['page', 'post']);
});

it('excludes draft content from search', function (): void {
    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');

    Page::factory()->create(['team_id' => $this->teamA->id, 'title' => 'Laravel Draft', 'slug' => 'laravel-draft']);

    $this->getJson('/api/v1/search?q=Laravel')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('does not leak another tenant\'s content in search', function (): void {
    Page::factory()->published()->create(['team_id' => $this->teamB->id, 'title' => 'Laravel Secret', 'slug' => 'laravel-secret']);

    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');

    $this->getJson('/api/v1/search?q=Laravel')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('ranks a title match above a body-only match', function (): void {
    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');

    Page::factory()->published()->create(['team_id' => $this->teamA->id, 'title' => 'Other', 'slug' => 'body-match', 'content' => 'all about Laravel', 'excerpt' => 'none']);
    Page::factory()->published()->create(['team_id' => $this->teamA->id, 'title' => 'Laravel Handbook', 'slug' => 'title-match', 'content' => 'x', 'excerpt' => 'none']);

    $response = $this->getJson('/api/v1/search?q=Laravel');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.slug', 'title-match')
        ->assertJsonPath('data.1.slug', 'body-match');
});

it('paginates search results with per_page', function (): void {
    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');

    Page::factory()->published()->count(9)->create(['team_id' => $this->teamA->id, 'title' => fn (): string => 'Laravel '.fake()->unique()->word()]);

    $this->getJson('/api/v1/search?q=Laravel&per_page=4')
        ->assertOk()
        ->assertJsonCount(4, 'data')
        ->assertJsonPath('meta.per_page', 4)
        ->assertJsonPath('meta.total', 9);
});
