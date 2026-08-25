<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentFederation\Services\ContentFederationService;

uses(RefreshDatabase::class);

it('registers sources, ingests normalized references, and tracks health', function (): void {
    $service = app(ContentFederationService::class);
    $source = $service->source(['name' => 'Remote CMS', 'adapter' => 'json-api', 'endpoint' => 'https://example.test/api'], 3);
    $reference = $service->ingest($source, 'article', '42', ['title' => 'Federated article'], 'v1');

    expect($reference->payload)->toBe(['title' => 'Federated article'])
        ->and($source->fresh()->status)->toBe('healthy')
        ->and($service->revalidate($reference))->toBeTrue();
});

it('serves cached fallback and rejects empty remote references', function (): void {
    $service = app(ContentFederationService::class);
    $source = $service->source(['name' => 'Remote CMS', 'adapter' => 'json-api'], 3);

    expect(fn () => $service->ingest($source, 'article', '42', []))->toThrow(ValidationException::class);
    $reference = $service->ingest($source, 'article', '42', ['title' => 'Cached']);
    expect($service->fallback($source, 'article', '42')->id)->toBe($reference->id)
        ->and($source->fresh()->status)->toBe('degraded');
});
