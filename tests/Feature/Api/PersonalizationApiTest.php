<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Personalization\Models\Audience;

uses(RefreshDatabase::class);

it('returns a safe audience read model and evaluates a decision', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read'], 'sanctum');
    $audience = Audience::create(['name' => 'Pro users', 'key' => 'pro-users', 'rules' => ['plan' => 'pro'], 'team_id' => $team->id]);
    $audience->variants()->create(['key' => 'welcome', 'payload' => ['headline' => 'Welcome'], 'priority' => 10, 'team_id' => $team->id]);

    $this->getJson('/api/v1/cms/personalization/audiences/pro-users')
        ->assertOk()->assertJsonPath('data.key', 'pro-users');
    $this->postJson('/api/v1/cms/personalization/audiences/pro-users/decision', [
        'context' => ['plan' => 'pro'], 'subject_key' => 'user-1', 'consent' => true,
    ])->assertOk()->assertJsonPath('data.variant_key', 'welcome')->assertJsonPath('data.payload.headline', 'Welcome');
});
