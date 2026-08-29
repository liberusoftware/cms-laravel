<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentLocking\Services\ContentLockingService;

uses(RefreshDatabase::class);

it('acquires, renews, compares, and releases an edit lock', function (): void {
    $service = app(ContentLockingService::class);
    $lock = $service->acquire('page', '42', 3, 7, ['title' => 'Draft']);

    expect($lock->holder_id)->toBe(7)
        ->and($service->compare($lock, ['title' => 'Changed'])['conflicted'])->toBeTrue()
        ->and($service->renew($lock, $lock->token)->expires_at->isFuture())->toBeTrue();

    $service->release($lock, $lock->token);
    expect($lock->fresh())->toBeNull();
});

it('prevents takeover and rejects invalid merge tokens', function (): void {
    $service = app(ContentLockingService::class);
    $lock = $service->acquire('page', '42', 3, 7, ['title' => 'Draft']);

    expect(fn () => $service->acquire('page', '42', 3, 8))->toThrow(ValidationException::class)
        ->and(fn () => $service->renew($lock, 'wrong-token'))->toThrow(ValidationException::class)
        ->and(fn () => $service->merge($lock, ['title' => 'Draft'], 'wrong-token'))->toThrow(ValidationException::class);
});

it('rejects invalid renewal durations', function (): void {
    $service = app(ContentLockingService::class);
    $lock = $service->acquire('page', '42', 3, 7);

    expect(fn () => $service->renew($lock, $lock->token, 0))
        ->toThrow(ValidationException::class);
});
