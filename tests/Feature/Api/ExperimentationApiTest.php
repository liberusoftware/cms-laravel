<?php

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Experimentation\Services\ExperimentationService;

uses(RefreshDatabase::class);

it('exposes experiment configuration and deterministic allocation through the API', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read', 'content:write'], 'sanctum');
    $service = app(ExperimentationService::class);
    $experiment = $service->create(['key' => 'api-test', 'name' => 'API Test', 'variants' => [['key' => 'a', 'weight' => 50], ['key' => 'b', 'weight' => 50]]], $team->id);
    $service->start($experiment);
    $this->getJson('/api/v1/cms/experimentation/api-test')->assertOk()->assertJsonPath('data.type', 'cms-experimentation');
    $this->postJson('/api/v1/cms/experimentation/api-test/allocate', ['subject_key' => 'subject'])->assertOk()->assertJsonPath('data.experiment_key', 'api-test');
});
