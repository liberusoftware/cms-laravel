<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates WordPress migration runs and records through the versioned API', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read', 'content:write', 'content:process'], 'sanctum');
    $response = $this->postJson('/api/v1/cms/wordpress-migration', ['source_url' => 'https://example.test/wp-json', 'options' => ['preserve_ids' => true]])->assertCreated()->assertJsonPath('data.type', 'cms-wordpress-migration');
    $publicId = $response->json('data.id');

    $this->postJson("/api/v1/cms/wordpress-migration/{$publicId}/records", ['record_type' => 'post', 'source_id' => '42', 'payload' => ['title' => 'Imported']])->assertCreated()->assertJsonPath('data.source_id', '42');
    $this->getJson("/api/v1/cms/wordpress-migration/{$publicId}/records")->assertOk()->assertJsonPath('data.0.record_type', 'post');
});
