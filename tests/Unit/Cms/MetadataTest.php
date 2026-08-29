<?php

declare(strict_types=1);

namespace Tests\Unit\Cms;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Liberu\Cms\Metadata\Services\MetadataService;

uses(RefreshDatabase::class);

it('stores, reads, replaces, and removes metadata', function (): void {
    $service = app(MetadataService::class);
    $service->set('content_entry', 7, 'seo.title', 'Hello', 10);
    $service->set('content_entry', 7, 'seo.title', 'Updated', 10);

    expect($service->get('content_entry', 7, 'seo.title'))->toBe('Updated')
        ->and($service->all('content_entry', 7))->toHaveKey('seo.title')
        ->and($service->remove('content_entry', 7, 'seo.title'))->toBeTrue()
        ->and($service->get('content_entry', 7, 'seo.title'))->toBeNull();
});

it('keeps metadata isolated by the active tenant', function (): void {
    $context = app(TenantContextInterface::class);
    $service = app(MetadataService::class);
    $context->setTenantId(10);
    $service->set('content_entry', 7, 'seo.title', 'Tenant A');
    $context->setTenantId(20);
    $service->set('content_entry', 7, 'seo.title', 'Tenant B');

    expect($service->get('content_entry', 7, 'seo.title'))->toBe('Tenant B');
    $context->setTenantId(null);
});

it('rejects unsafe metadata keys and subjects', function (): void {
    $service = app(MetadataService::class);

    expect(fn () => $service->set('content entry', 1, 'title', 'x'))->toThrow(ValidationException::class)
        ->and(fn () => $service->set('content_entry', 1, 'bad key', 'x'))->toThrow(ValidationException::class);
});
