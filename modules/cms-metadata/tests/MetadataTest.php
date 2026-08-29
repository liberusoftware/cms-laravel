<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Metadata\Services\MetadataService;

uses(RefreshDatabase::class);

it('stores, reads, replaces, and removes tenant-scoped metadata', function (): void {
    $service = app(MetadataService::class);
    $service->set('content_entry', 7, 'seo.title', 'Hello', 10);
    $service->set('content_entry', 7, 'seo.title', 'Updated', 10);

    expect($service->get('content_entry', 7, 'seo.title'))->toBe('Updated')
        ->and($service->all('content_entry', 7))->toHaveKey('seo.title')
        ->and($service->remove('content_entry', 7, 'seo.title'))->toBeTrue()
        ->and($service->get('content_entry', 7, 'seo.title'))->toBeNull();
});

it('rejects unsafe metadata keys and subjects', function (): void {
    $service = app(MetadataService::class);

    expect(fn () => $service->set('content entry', 1, 'title', 'x'))->toThrow(ValidationException::class)
        ->and(fn () => $service->set('content_entry', 1, 'bad key', 'x'))->toThrow(ValidationException::class);
});
