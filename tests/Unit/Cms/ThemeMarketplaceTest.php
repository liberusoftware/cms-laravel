<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ThemeMarketplace\Services\ThemeMarketplaceService;

uses(RefreshDatabase::class);

it('reviews, installs, updates, and rates a compatible theme', function (): void {
    $service = app(ThemeMarketplaceService::class);
    $theme = $service->publish(['key' => 'clean', 'name' => 'Clean', 'version' => '1.0.0', 'author' => 'Liberu', 'license' => 'MIT', 'compatibility' => ['cms' => '1.0.0', 'features' => ['blade']]]);
    $service->reviewSecurity($theme, 'approved');
    $installation = $service->install($theme, 'main', '1.2.0', ['blade']);
    $next = $service->publish(['key' => 'clean', 'name' => 'Clean', 'version' => '1.1.0', 'author' => 'Liberu', 'license' => 'MIT', 'compatibility' => ['cms' => '1.0.0', 'features' => ['blade']]]);
    $service->reviewSecurity($next, 'approved');
    $service->update($installation, $next, '1.2.0', ['blade']);
    $service->rate($next, 'user', 5, 5, 'Excellent');

    expect($installation->fresh()->installed_version)->toBe('1.1.0')->and($service->ratingSummary($next))->toBe(['average' => 5.0, 'count' => 1]);
});

it('rejects unreviewed or incompatible themes and invalid ratings', function (): void {
    $service = app(ThemeMarketplaceService::class);
    $theme = $service->publish(['key' => 'legacy', 'name' => 'Legacy', 'version' => '1.0.0', 'author' => 'A', 'compatibility' => ['cms' => '2.0.0']]);
    expect(fn () => $service->install($theme, 'main', '1.0.0'))->toThrow(ValidationException::class);
    expect(fn () => $service->rate($theme, 'user', 1, 6))->toThrow(ValidationException::class);
});

it('requires child themes to reference an available published parent', function (): void {
    $service = app(ThemeMarketplaceService::class);

    expect(fn () => $service->publish(['key' => 'child', 'name' => 'Child', 'version' => '1.0.0', 'author' => 'A', 'parent_key' => 'missing']))
        ->toThrow(ValidationException::class);

    $parent = $service->publish(['key' => 'parent', 'name' => 'Parent', 'version' => '1.0.0', 'author' => 'A']);
    $child = $service->publish(['key' => 'child', 'name' => 'Child', 'version' => '1.0.0', 'author' => 'A', 'parent_key' => $parent->key]);

    expect($child->parent_key)->toBe('parent');
});
