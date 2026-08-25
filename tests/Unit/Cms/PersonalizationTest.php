<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Personalization\Models\Audience;
use Liberu\Cms\Personalization\Services\DecisionEngine;
use Liberu\Cms\Personalization\Services\PersonalizationService;

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

it('validates audience and variant mutations and maintains one fallback', function (): void {
    $service = app(PersonalizationService::class);
    $audience = $service->createAudience(['name' => 'Launch', 'key' => 'launch', 'rules' => ['plan' => 'pro']]);
    $first = $service->saveVariant($audience, ['key' => 'default', 'payload' => ['theme' => 'base'], 'fallback' => true]);
    $second = $service->saveVariant($audience, ['key' => 'experiment', 'payload' => ['theme' => 'new'], 'fallback' => true, 'holdout_percent' => 50]);

    expect($first->fresh()->fallback)->toBeFalse()
        ->and($second->fresh()->fallback)->toBeTrue()
        ->and(fn () => $service->saveVariant($audience, ['key' => 'invalid', 'payload' => [], 'holdout_percent' => 101]))->toThrow(ValidationException::class);
});

it('redacts decision subjects and unapproved context from evidence', function (): void {
    $audience = Audience::create(['name' => 'Evidence', 'key' => 'evidence', 'rules' => []]);
    $audience->variants()->create(['key' => 'default', 'payload' => ['ok' => true]]);

    app(DecisionEngine::class)->decide('evidence', ['plan' => 'pro', 'email' => 'private@example.test'], 'person-1');
    $decision = $audience->decisions()->latest('id')->first();

    expect($decision)->not->toBeNull()
        ->and($decision->subject_key)->not->toBe('person-1')
        ->and($decision->context)->toBe(['plan' => 'pro']);
});
