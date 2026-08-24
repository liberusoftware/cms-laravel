<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\StaticPublishing\Services\StaticPublishingService;

uses(RefreshDatabase::class);
it('builds full, incremental, and preview manifests with diagnostics', function (): void {
    $service = app(StaticPublishingService::class);
    $full = $service->build([['path' => '/'], ['path' => '/about'], ['path' => 'invalid']], 'docs');
    $incremental = $service->build([['path' => '/about']], 'docs', 'incremental', 'cdn', $full);
    expect($full->state)->toBe('published')->and($service->diagnostics($full)['route_count'])->toBe(2)->and($service->rollback($incremental)->state)->toBe('rolled_back');
});
it('validates build kinds and invalidation paths', function (): void {
    $service = app(StaticPublishingService::class);
    expect(fn () => $service->build([], null, 'unknown'))->toThrow(ValidationException::class);
    $build = $service->build([], 'docs');
    expect(fn () => $service->invalidate($build, 'relative'))->toThrow(ValidationException::class);
});
