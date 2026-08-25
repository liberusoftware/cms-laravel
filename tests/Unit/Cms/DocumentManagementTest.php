<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\DocumentManagement\Services\DocumentManagementService;

uses(RefreshDatabase::class);

it('creates and transitions a tenant document', function (): void {
    $service = app(DocumentManagementService::class);
    $document = $service->create(['title' => 'Guide', 'slug' => 'guide', 'mime_type' => 'application/pdf'], 10);
    expect($document->status)->toBe('draft')->and($document->retention_until)->not->toBeNull();
    expect($service->transition($document, 'ready')->status)->toBe('ready');
});

it('rejects invalid document lifecycle states', function (): void {
    expect(fn () => app(DocumentManagementService::class)->create(['title' => 'Bad', 'slug' => 'bad', 'status' => 'unknown']))->toThrow(ValidationException::class);
});
