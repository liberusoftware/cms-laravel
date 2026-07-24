<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Pages\Models\Page;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('permission.teams', true);
});

it('authenticates with a bearer Delivery token and scopes to its team', function (): void {
    $team = Team::factory()->create();
    Page::factory()->published()->create(['team_id' => $team->id, 'slug' => 'welcome']);

    $token = $team->createToken('delivery', ['content:read'])->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/v1/pages/welcome')
        ->assertOk()
        ->assertJsonPath('data.slug', 'welcome');
});

it('issues a Delivery token from the console command', function (): void {
    $team = Team::factory()->create();

    $this->artisan('cms-api:issue-token', ['team' => $team->id])
        ->assertSuccessful();

    expect($team->tokens()->count())->toBe(1);
});

it('fails the token command for an unknown team', function (): void {
    $this->artisan('cms-api:issue-token', ['team' => 999999])
        ->assertFailed();
});

it('rate limits per token with a 429 and Retry-After header', function (): void {
    config()->set('cms-api.rate_limit', 1);

    $team = Team::factory()->create();
    Page::factory()->published()->create(['team_id' => $team->id, 'slug' => 'welcome']);
    $token = $team->createToken('delivery', ['content:read'])->plainTextToken;

    $this->withToken($token)->getJson('/api/v1/pages')->assertOk();

    $this->withToken($token)->getJson('/api/v1/pages')
        ->assertStatus(429)
        ->assertHeader('Retry-After');
});
