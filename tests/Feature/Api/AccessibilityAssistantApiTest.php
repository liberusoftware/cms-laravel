<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('analyzes content through the accessibility assistant API', function (): void {
    $team = Team::factory()->create();
    $token = $team->createToken('accessibility-assistant', ['content:read'])->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/cms/accessibility-assistant/analyze', ['html' => '<img src="hero.jpg">'])
        ->assertOk()
        ->assertJsonPath('data.findings.0.code', 'image-alt');
});
