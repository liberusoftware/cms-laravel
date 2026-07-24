<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Pages\Models\Page;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->teamA = Team::factory()->create();
    $this->teamB = Team::factory()->create();
});

function actAsDeliveryClient(Team $team): void
{
    Sanctum::actingAs($team, ['content:read'], 'sanctum');
}

it('rejects an unauthenticated request with 401', function (): void {
    $this->getJson('/api/v1/pages')->assertUnauthorized();
});

it('rejects an unauthenticated non-JSON request with 401 rather than a redirect', function (): void {
    $this->get('/api/v1/pages')->assertUnauthorized();
});

it('enables CORS for the API paths', function (): void {
    actAsDeliveryClient($this->teamA);

    $this->getJson('/api/v1/pages', ['Origin' => 'https://frontend.example'])
        ->assertOk()
        ->assertHeader('Access-Control-Allow-Origin', '*');
});

it('returns published pages for the token\'s tenant', function (): void {
    actAsDeliveryClient($this->teamA);

    Page::factory()->published()->create(['team_id' => $this->teamA->id, 'title' => 'Live One', 'slug' => 'live-one']);
    Page::factory()->create(['team_id' => $this->teamA->id, 'title' => 'A Draft', 'slug' => 'a-draft']);

    $response = $this->getJson('/api/v1/pages');

    $response->assertOk()
        ->assertJsonPath('data.0.slug', 'live-one')
        ->assertJsonMissing(['slug' => 'a-draft'])
        ->assertJsonCount(1, 'data');
});

it('fetches a single published page by slug', function (): void {
    actAsDeliveryClient($this->teamA);

    Page::factory()->published()->create(['team_id' => $this->teamA->id, 'title' => 'About', 'slug' => 'about']);

    $this->getJson('/api/v1/pages/about')
        ->assertOk()
        ->assertJsonPath('data.slug', 'about')
        ->assertJsonPath('data.title', 'About');
});

it('returns 404 for a draft page fetched by slug', function (): void {
    actAsDeliveryClient($this->teamA);

    Page::factory()->create(['team_id' => $this->teamA->id, 'slug' => 'secret-draft']);

    $this->getJson('/api/v1/pages/secret-draft')->assertNotFound();
});

it('returns 404 for an unknown slug', function (): void {
    actAsDeliveryClient($this->teamA);

    $this->getJson('/api/v1/pages/nope')->assertNotFound();
});

it('does not leak another tenant\'s page (cross-tenant is 404)', function (): void {
    Page::factory()->published()->create(['team_id' => $this->teamB->id, 'slug' => 'team-b-page']);

    actAsDeliveryClient($this->teamA);

    $this->getJson('/api/v1/pages/team-b-page')->assertNotFound();
    $this->getJson('/api/v1/pages')->assertOk()->assertJsonCount(0, 'data');
});

it('returns content as sanitized HTML', function (): void {
    actAsDeliveryClient($this->teamA);

    Page::factory()->published()->create([
        'team_id' => $this->teamA->id,
        'slug' => 'xss',
        'content' => '<p>Visible safe text</p><script>alert("pwned")</script><p onclick="steal()">handler text</p>',
    ]);

    $response = $this->getJson('/api/v1/pages/xss');

    $content = $response->json('data.content');

    expect($content)
        ->toContain('Visible safe text')
        ->toContain('handler text')
        ->not->toContain('alert("pwned")')
        ->not->toContain('onclick')
        ->not->toContain('steal()');
});

it('paginates with consistent metadata and honors per_page', function (): void {
    actAsDeliveryClient($this->teamA);

    Page::factory()->published()->count(20)->create(['team_id' => $this->teamA->id]);

    $response = $this->getJson('/api/v1/pages?per_page=5');

    $response->assertOk()
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.per_page', 5)
        ->assertJsonPath('meta.total', 20)
        ->assertJsonStructure(['data', 'links', 'meta' => ['current_page', 'per_page', 'total']]);
});

it('caps per_page at the configured maximum', function (): void {
    config()->set('cms-api.pagination.max', 100);
    actAsDeliveryClient($this->teamA);

    Page::factory()->published()->count(3)->create(['team_id' => $this->teamA->id]);

    $this->getJson('/api/v1/pages?per_page=1000')
        ->assertOk()
        ->assertJsonPath('meta.per_page', 100);
});
