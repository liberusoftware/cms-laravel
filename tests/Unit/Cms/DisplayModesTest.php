<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\DisplayModes\Models\DisplayMode;
use Liberu\Cms\DisplayModes\Services\DisplayModesService;

uses(RefreshDatabase::class);

it('selects a responsive display mode configuration for a tenant', function (): void {
    $mode = app(DisplayModesService::class)->create([
        'name' => 'Article default',
        'slug' => 'default',
        'content_type' => 'article',
        'configuration' => ['layout' => 'stacked'],
        'responsive_variants' => ['mobile' => ['layout' => 'compact']],
    ], 10);

    expect($mode)->toBeInstanceOf(DisplayMode::class)
        ->and(app(DisplayModesService::class)->select('article', 10, 'default', 'mobile')?->configuration)
        ->toBe(['layout' => 'compact']);
});

it('validates mode type and scopes selection to the tenant', function (): void {
    expect(fn () => app(DisplayModesService::class)->create(['name' => 'Bad', 'slug' => 'bad', 'content_type' => 'article', 'mode_type' => 'invalid']))
        ->toThrow(ValidationException::class);

    app(DisplayModesService::class)->create(['name' => 'Private', 'slug' => 'default', 'content_type' => 'article'], 10);

    expect(app(DisplayModesService::class)->select('article', 11))->toBeNull();
});
