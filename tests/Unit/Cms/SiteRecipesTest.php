<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\SiteRecipes\Services\SiteRecipeService;

uses(RefreshDatabase::class);
it('versions, publishes, exports, and archives recipe bundles', function (): void {
    $service = app(SiteRecipeService::class);
    $recipe = $service->create('starter', 'Starter');
    $service->version($recipe, ['modules' => ['pages'], 'configuration' => ['locale' => 'en'], 'themes' => ['key' => 'default']]);
    expect($service->publish($recipe)->status)->toBe('published')->and($service->export($recipe)['version'])->toBe(1)->and($service->archive($recipe)->status)->toBe('archived');
});
it('requires valid bundle sections and a version before publishing', function (): void {
    $service = app(SiteRecipeService::class);
    $recipe = $service->create('empty', 'Empty');
    expect(fn () => $service->publish($recipe))->toThrow(ValidationException::class)->and(fn () => $service->version($recipe, ['menus' => 'invalid']))->toThrow(ValidationException::class);
});
