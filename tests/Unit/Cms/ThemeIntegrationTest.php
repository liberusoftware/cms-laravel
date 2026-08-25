<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ThemeIntegration\Services\ThemeIntegrationService;

uses(RefreshDatabase::class);

it('selects themes by site and channel with a safe fallback and components', function (): void {
    $service = app(ThemeIntegrationService::class);
    $site = $service->bind('main', null, 'base', 'default');
    $service->bind('main', 'mobile', 'mobile', 'base');
    $service->registerComponent('mobile', 'header', 'navigation', ['required' => ['items']]);

    expect($service->effectiveTheme('main', 'mobile'))->toBe('mobile')
        ->and($service->effectiveTheme('main', 'unknown'))->toBe('base')
        ->and($service->components('mobile', 'header'))->toHaveCount(1)
        ->and($service->preview($site, $service->previewToken($site)))->toBeTrue();
});
