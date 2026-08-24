<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\OfflineAndPwa\Services\OfflineAndPwaService;

uses(RefreshDatabase::class);

it('configures a PWA and renders its manifest and service worker', function (): void {
    $service = app(OfflineAndPwaService::class);
    $configuration = $service->configure('main', 'Liberu CMS', 'CMS', attributes: ['start_url' => '/', 'offline_url' => '/offline', 'icon_url' => '/icon.png']);
    $service->setCachePolicy($configuration, ['precache' => ['/', '/offline'], 'max_entries' => 25]);
    $service->publishUpdate($configuration, '2026.08.24');

    expect($service->manifest($configuration->fresh()))->toMatchArray(['name' => 'Liberu CMS', 'short_name' => 'CMS', 'start_url' => '/'])
        ->and($service->serviceWorker($configuration->fresh()))->toContain('2026.08.24')->toContain('/offline');
});

it('rejects invalid install metadata and cache limits', function (): void {
    $service = app(OfflineAndPwaService::class);
    expect(fn () => $service->configure('main', 'Name', 'This name is too long'))->toThrow(ValidationException::class);
    $configuration = $service->configure('main', 'Name', 'PWA');
    expect(fn () => $service->setCachePolicy($configuration, ['max_entries' => 0]))->toThrow(ValidationException::class);
    expect(fn () => $service->configure('other', 'Name', 'PWA', attributes: ['offline_url' => 'https://example.test/offline']))->toThrow(ValidationException::class);
});
