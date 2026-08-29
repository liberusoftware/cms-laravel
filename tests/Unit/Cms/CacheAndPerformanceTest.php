<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\CacheAndPerformance\Services\CacheAndPerformanceService;

uses(RefreshDatabase::class);

it('warms a tagged cache entry, records diagnostics, and invalidates it idempotently', function (): void {
    Cache::flush();
    $service = app(CacheAndPerformanceService::class);
    $first = $service->remember(8, 'page:home', 'page', 60, fn () => ['html' => 'home'], ['site:8', 'page']);
    $second = $service->remember(8, 'page:home', 'page', 60, fn () => ['html' => 'changed'], ['site:8', 'page']);
    $invalidation = $service->invalidate(8, ['site:8'], [], 'invalidate-1');
    $same = $service->invalidate(8, ['site:8'], [], 'invalidate-1');

    expect($first['hit'])->toBeFalse()->and($second['hit'])->toBeTrue()->and($invalidation->invalidated_count)->toBe(1)->and($same->getKey())->toBe($invalidation->getKey())->and($service->diagnostics(8))->toMatchArray(['entries' => 1, 'hits' => 1, 'misses' => 1]);
});

it('validates cache contracts', function (): void {
    $service = app(CacheAndPerformanceService::class);
    expect(fn () => $service->remember(8, '', 'page', 60, fn () => 'x'))->toThrow(ValidationException::class)
        ->and(fn () => $service->invalidate(8, [], [], 'x'))->toThrow(ValidationException::class);
});
