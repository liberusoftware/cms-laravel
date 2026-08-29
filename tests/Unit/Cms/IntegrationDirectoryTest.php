<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\IntegrationDirectory\Services\IntegrationDirectoryService;

uses(RefreshDatabase::class);

it('registers and tracks integration lifecycle and health', function (): void {
    $service = app(IntegrationDirectoryService::class);
    $integration = $service->register('search-service', 'Search', 'Acme Search', ['endpoint' => 'https://search.example.test'], 10);
    $service->enable($integration);
    $service->health($integration->refresh(), 'healthy', 'Connected');

    expect($service->enabled())->toHaveCount(1)
        ->and($integration->refresh()->health_status)->toBe('healthy');
});

it('rejects unsafe integration keys and health states', function (): void {
    $service = app(IntegrationDirectoryService::class);
    expect(fn () => $service->register('Bad Key', 'Bad', 'Provider'))->toThrow(ValidationException::class);
    $integration = $service->register('valid-key', 'Valid', 'Provider');
    expect(fn () => $service->health($integration, 'broken'))->toThrow(ValidationException::class);
});
