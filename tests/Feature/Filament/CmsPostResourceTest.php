<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Posts\Filament\Pages\ListPosts;
use Liberu\Cms\Posts\Filament\PostResource;
use Liberu\Cms\Posts\Models\Post;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
    grantCmsPermissions($this->user, $this->team, ['posts.view', 'posts.create', 'posts.update', 'posts.delete']);

    $panel = Filament::getPanel('app');
    Filament::setCurrentPanel($panel);

    // Livewire::test() does not run Panel::boot(), so register the tenancy scope
    // and creation observer the way a real request would.
    PostResource::registerTenancyModelGlobalScope($panel);
    PostResource::observeTenancyModelCreation($panel);

    Filament::setTenant($this->team);
});

it('renders the posts list', function (): void {
    Livewire::test(ListPosts::class)->assertSuccessful();
});

it('lists post records', function (): void {
    $posts = Post::factory()->count(3)->create(['team_id' => $this->team->id]);

    Livewire::test(ListPosts::class)->assertCanSeeTableRecords($posts);
});

it('creates a post through the modal', function (): void {
    Livewire::test(ListPosts::class)
        ->callAction('create', [
            'title' => 'Hello World Post',
            'status' => 'published',
            'is_featured' => true,
        ]);

    $this->assertDatabaseHas('cms_posts', [
        'title' => 'Hello World Post',
        'is_featured' => true,
    ]);
});

it('edits a post through the row action', function (): void {
    $post = Post::factory()->create(['title' => 'Draft Title', 'team_id' => $this->team->id]);

    Livewire::test(ListPosts::class)
        ->callTableAction('edit', $post, ['title' => 'Published Title']);

    expect($post->fresh()->title)->toBe('Published Title');
});

it('deletes a post through the row action', function (): void {
    $post = Post::factory()->create(['team_id' => $this->team->id]);

    Livewire::test(ListPosts::class)->callTableAction('delete', $post);

    $this->assertModelMissing($post);
});
