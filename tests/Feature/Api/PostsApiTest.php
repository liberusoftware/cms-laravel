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
    Sanctum::actingAs($this->teamA, ['content:read'], 'sanctum');
});

it('returns published posts newest first and hides drafts', function (): void {
    $older = Post::factory()->published()->create(['team_id' => $this->teamA->id, 'slug' => 'older', 'published_at' => now()->subDay()]);
    $newer = Post::factory()->published()->create(['team_id' => $this->teamA->id, 'slug' => 'newer', 'published_at' => now()]);
    Post::factory()->create(['team_id' => $this->teamA->id, 'slug' => 'draft']);

    $this->getJson('/api/v1/posts')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.slug', 'newer')
        ->assertJsonPath('data.1.slug', 'older')
        ->assertJsonMissing(['slug' => 'draft']);
});

it('fetches a single published post with categories and tags inline', function (): void {
    $post = Post::factory()->published()->create(['team_id' => $this->teamA->id, 'slug' => 'hello-world']);
    $category = Category::factory()->create(['team_id' => $this->teamA->id, 'name' => 'News', 'slug' => 'news']);
    $tag = Tag::factory()->create(['team_id' => $this->teamA->id, 'name' => 'Laravel', 'slug' => 'laravel']);
    $post->categories()->attach($category);
    $post->tags()->attach($tag);

    $this->getJson('/api/v1/posts/hello-world')
        ->assertOk()
        ->assertJsonPath('data.slug', 'hello-world')
        ->assertJsonPath('data.categories.0.slug', 'news')
        ->assertJsonPath('data.tags.0.slug', 'laravel')
        ->assertJsonMissingPath('data.team_id');
});

it('returns 404 for a draft post and for an unknown slug', function (): void {
    Post::factory()->create(['team_id' => $this->teamA->id, 'slug' => 'not-live']);

    $this->getJson('/api/v1/posts/not-live')->assertNotFound();
    $this->getJson('/api/v1/posts/ghost')->assertNotFound();
});

it('does not leak another tenant\'s post', function (): void {
    Post::factory()->published()->create(['team_id' => $this->teamB->id, 'slug' => 'team-b-post']);

    $this->getJson('/api/v1/posts/team-b-post')->assertNotFound();
    $this->getJson('/api/v1/posts')->assertOk()->assertJsonCount(0, 'data');
});

it('filters posts by category slug', function (): void {
    $inCategory = Post::factory()->published()->create(['team_id' => $this->teamA->id, 'slug' => 'in-cat']);
    $outCategory = Post::factory()->published()->create(['team_id' => $this->teamA->id, 'slug' => 'out-cat']);
    $category = Category::factory()->create(['team_id' => $this->teamA->id, 'slug' => 'tech']);
    $inCategory->categories()->attach($category);

    $this->getJson('/api/v1/posts?category=tech')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.slug', 'in-cat');
});

it('filters posts by tag slug', function (): void {
    $tagged = Post::factory()->published()->create(['team_id' => $this->teamA->id, 'slug' => 'tagged']);
    Post::factory()->published()->create(['team_id' => $this->teamA->id, 'slug' => 'untagged']);
    $tag = Tag::factory()->create(['team_id' => $this->teamA->id, 'slug' => 'php']);
    $tagged->tags()->attach($tag);

    $this->getJson('/api/v1/posts?tag=php')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.slug', 'tagged');
});
