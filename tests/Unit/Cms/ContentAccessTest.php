<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentAccess\Services\ContentAccessService;

uses(RefreshDatabase::class);

it('enforces private, audience, scheduled, and preview access rules', function (): void {
    $service = app(ContentAccessService::class);
    $service->rule('page', 'private', ['visibility' => 'private'], 3);
    $service->rule('page', 'audience', ['visibility' => 'audience', 'audiences' => ['members']], 3);
    $service->rule('page', 'preview', ['visibility' => 'private', 'preview_allowed' => true], 3);

    expect($service->canAccess('page', 'private', 3))->toBeFalse()
        ->and($service->canAccess('page', 'audience', 3, ['guests']))->toBeFalse()
        ->and($service->canAccess('page', 'audience', 3, ['members']))->toBeTrue()
        ->and($service->canAccess('page', 'preview', 3, [], true))->toBeTrue();
});

it('creates single-use private links and rejects invalid schedules', function (): void {
    $service = app(ContentAccessService::class);
    $token = $service->createPrivateLink('page', 'secret', 3, 1);

    expect($service->canAccess('page', 'secret', 3, [], false, $token))->toBeTrue()
        ->and($service->canAccess('page', 'secret', 3, [], false, $token))->toBeFalse();

    expect(fn () => $service->rule('page', 'bad', ['visibility' => 'scheduled', 'available_from' => '2026-08-26', 'available_until' => '2026-08-25'], 3))
        ->toThrow(ValidationException::class);
});
