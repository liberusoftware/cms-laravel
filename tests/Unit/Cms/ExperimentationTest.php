<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Experimentation\Services\ExperimentationService;

uses(RefreshDatabase::class);

it('allocates deterministically, records goals, and promotes a winner with history', function (): void {
    $service = app(ExperimentationService::class);
    $experiment = $service->create(['key' => 'hero', 'name' => 'Hero test', 'goals' => ['conversion'], 'guardrails' => ['error_rate' => 0.05], 'analysis_policy' => ['confidence' => 0.95], 'variants' => [['key' => 'a', 'name' => 'Control', 'weight' => 50], ['key' => 'b', 'name' => 'Variant', 'weight' => 50]]]);
    $service->start($experiment);
    $first = $service->allocate($experiment->fresh('variants'), 'subject-1');
    $second = $service->allocate($experiment->fresh('variants'), 'subject-1');
    $service->observe($experiment, $first, 'subject-1', 'conversion', 1);
    $service->promote($experiment, $first, 'Goal met', 'user', 1);

    expect($first->key)->toBe($second->key)
        ->and($experiment->fresh()->status)->toBe('promoted')
        ->and($experiment->fresh()->winner_variant_key)->toBe($first->key)
        ->and($experiment->promotions()->count())->toBe(1);
});

it('rejects invalid weights and prevents promotion of an unrelated variant', function (): void {
    $service = app(ExperimentationService::class);
    expect(fn () => $service->create(['key' => 'invalid', 'name' => 'Invalid', 'variants' => [['key' => 'a', 'weight' => 70], ['key' => 'b', 'weight' => 20]]]))->toThrow(ValidationException::class);
    $first = $service->create(['key' => 'first', 'name' => 'First', 'variants' => [['key' => 'a', 'weight' => 50], ['key' => 'b', 'weight' => 50]]]);
    $other = $service->create(['key' => 'other', 'name' => 'Other', 'variants' => [['key' => 'a', 'weight' => 50], ['key' => 'b', 'weight' => 50]]]);
    $service->start($first);
    expect(fn () => $service->promote($first, $other->variants->first()))->toThrow(ValidationException::class);
});
