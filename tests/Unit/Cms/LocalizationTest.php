<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Localization\Services\LocalizationService;

uses(RefreshDatabase::class);

it('stores locale variants and resolves fallback translations', function (): void {
    $service = app(LocalizationService::class);
    $service->locale('en-US', 7, null, 'ltr');
    $service->locale('ar', 7, 'en-US', 'rtl');
    $service->variant('post', 'post-1', 'title', 'en-US', 'Hello', 7, 'hello', 'complete');

    expect($service->resolve('post', 'post-1', 'title', 'ar', 7, 'en-US')?->value)->toBe('Hello')
        ->and($service->completeness('post', 'post-1', 'ar', 7))->toBe(0.0);
});

it('reports completeness and rejects invalid direction or locale', function (): void {
    $service = app(LocalizationService::class);
    $service->locale('en', 7);
    $service->variant('page', 'page-1', 'title', 'en', 'Welcome', 7, null, 'complete');

    expect($service->completeness('page', 'page-1', 'en', 7))->toBe(100.0);
    expect(fn () => $service->locale('en', 7, null, 'sideways'))->toThrow(ValidationException::class);
    expect(fn () => $service->variant('page', 'page-1', 'title', 'not-a-locale', 'x', 7))->toThrow(ValidationException::class);
});
