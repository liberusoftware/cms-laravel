<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Posts\Models\Category;
use Liberu\Cms\Posts\Models\Post;
use Liberu\Cms\Posts\Models\Tag;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->teamA = Team::factory()->create();
    $this->teamB = Team::factory()->create();
});

it('forbids a read-only token from creating a post', function (): void {
    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');

    $this->postJson('/api/v1/posts', ['title' => 'X'])->assertForbidden();
});

it('creates a post and syncs only the tenant\'s taxonomy', function (): void {
    Sanctum::actingAs($this->teamA, ['content:read', 'content:write'], 'sanctum');

    $mine = Category::factory()->create(['team_id' => $this->teamA->id, 'slug' => 'mine']);
    $theirs = Category::factory()->create(['team_id' => $this->teamB->id, 'slug' => 'theirs']);
    $tag = Tag::factory()->create(['team_id' => $this->teamA->id, 'slug' => 'php']);

    $response = $this->postJson('/api/v1/posts', [
        'title' => 'Hello',
        'categories' => [$mine->id, $theirs->id],
        'tags' => [$tag->id],
    ]);

    $response->assertCreated()
        ->assertJsonCount(1, 'data.categories')
        ->assertJsonPath('data.categories.0.slug', 'mine')
        ->assertJsonPath('data.tags.0.slug', 'php');

    $post = Post::query()->firstOrFail();
    expect($post->team_id)->toBe($this->teamA->id)
        ->and($post->categories()->pluck('cms_categories.id')->all())->toBe([$mine->id]);
});

it('updates and deletes a post, isolating tenants', function (): void {
    Sanctum::actingAs($this->teamA, ['content:read', 'content:write'], 'sanctum');

    $post = Post::factory()->create(['team_id' => $this->teamA->id, 'slug' => 'mine']);
    $other = Post::factory()->create(['team_id' => $this->teamB->id, 'slug' => 'theirs']);

    $this->putJson("/api/v1/posts/{$post->id}", ['title' => 'Renamed'])
        ->assertOk()
        ->assertJsonPath('data.title', 'Renamed');

    $this->putJson("/api/v1/posts/{$other->id}", ['title' => 'Nope'])->assertNotFound();

    $this->deleteJson("/api/v1/posts/{$post->id}")->assertNoContent();
    $this->assertDatabaseMissing('cms_posts', ['id' => $post->id]);
});
