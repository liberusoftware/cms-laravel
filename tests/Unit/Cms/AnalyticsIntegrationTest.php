<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\AnalyticsIntegration\Services\AnalyticsIntegrationService;

uses(RefreshDatabase::class);

it('records consent-aware canonical events idempotently and aggregates a dashboard', function (): void {
    $service = app(AnalyticsIntegrationService::class);
    $accepted = $service->recordEvent(7, ['event_type' => 'view', 'event_name' => 'page_view', 'idempotency_key' => 'view-1', 'consent_granted' => true, 'payload' => ['path' => '/home']]);
    $same = $service->recordEvent(7, ['event_type' => 'view', 'event_name' => 'changed', 'idempotency_key' => 'view-1', 'consent_granted' => true, 'payload' => ['path' => '/other']]);
    $service->recordEvent(7, ['event_type' => 'conversion', 'event_name' => 'signup', 'idempotency_key' => 'conversion-1']);

    expect($same->getKey())->toBe($accepted->getKey())->and($same->event_name)->toBe('page_view')->and($same->payload)->toBe(['path' => '/home'])->and($service->dashboard(7))->toMatchArray(['total' => 2, 'accepted' => 1, 'suppressed' => 1]);
});

it('validates event contracts and mappings', function (): void {
    $service = app(AnalyticsIntegrationService::class);
    expect(fn () => $service->recordEvent(7, ['event_type' => 'unknown', 'event_name' => 'x', 'idempotency_key' => '1']))->toThrow(ValidationException::class)
        ->and(fn () => $service->saveMapping(7, ['event_type' => 'view']))->toThrow(ValidationException::class);
    expect($service->saveMapping(7, ['event_type' => 'view', 'provider' => 'generic', 'measurement_key' => 'site-view'])->measurement_key)->toBe('site-view');
});
