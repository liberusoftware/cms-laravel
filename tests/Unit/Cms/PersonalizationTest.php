<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Personalization\Models\Audience;
use Liberu\Cms\Personalization\Services\DecisionEngine;

uses(RefreshDatabase::class);

it('selects the highest priority eligible variant and records evidence', function (): void {
    $audience = Audience::create(['name' => 'Beta', 'key' => 'beta', 'rules' => ['plan' => 'pro']]);
    $audience->variants()->createMany([
        ['key' => 'fallback', 'payload' => ['message' => 'default'], 'fallback' => true, 'priority' => 0],
        ['key' => 'pro', 'payload' => ['message' => 'welcome'], 'priority' => 10],
    ]);

    $result = app(DecisionEngine::class)->decide('beta', ['plan' => 'pro'], 'user-1');

    expect($result['variant']?->key)->toBe('pro')->and($result['reason'])->toBe('eligible');
    $this->assertDatabaseHas('cms_personalization_decisions', ['audience_key' => 'beta', 'variant_key' => 'pro', 'reason' => 'eligible']);
});

it('fails closed for ineligible and consent-gated audiences', function (): void {
    Audience::create(['name' => 'Consent', 'key' => 'consent', 'rules' => ['country' => 'GB'], 'requires_consent' => true]);

    expect(app(DecisionEngine::class)->decide('consent', ['country' => 'GB'])['reason'])->toBe('consent_required')
        ->and(app(DecisionEngine::class)->decide('consent', ['country' => 'US'], null, true)['reason'])->toBe('ineligible');
});
