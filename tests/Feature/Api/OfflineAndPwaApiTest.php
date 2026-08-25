<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\OfflineAndPwa\Models\PwaConfiguration;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->team = Team::factory()->create();
    Sanctum::actingAs($this->team, ['content:read', 'content:write'], 'sanctum');
});

it('creates and updates a tenant-scoped PWA configuration through the API', function (): void {
    $data = $this->postJson('/api/v1/cms/offline-and-pwa', [
        'site_key' => 'main',
        'name' => 'Liberu CMS',
        'short_name' => 'CMS',
        'offline_url' => '/offline',
    ])->assertCreated()->json('data');

    $this->patchJson("/api/v1/cms/offline-and-pwa/{$data['id']}", ['name' => 'Updated CMS'])
        ->assertOk()->assertJsonPath('data.name', 'Updated CMS');

    $this->postJson("/api/v1/cms/offline-and-pwa/{$data['id']}/cache-policy", ['precache' => ['/'], 'max_entries' => 10])
        ->assertOk()->assertJsonPath('data.cache_policy.max_entries', 10);
});

it('does not expose another tenant configuration', function (): void {
    $other = Team::withoutEvents(fn (): Team => Team::factory()->create());
    PwaConfiguration::withoutEvents(fn () => PwaConfiguration::create(['team_id' => $other->id, 'site_key' => 'other', 'name' => 'Other', 'short_name' => 'Other']));

    $this->getJson('/api/v1/cms/offline-and-pwa')->assertOk()->assertJsonCount(0, 'data');
});
