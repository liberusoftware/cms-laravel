<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\SecurityOperations\Services\SecurityOperationsService;

uses(RefreshDatabase::class);

it('records integrity evidence, provenance, and advisories', function (): void {
    $service = app(SecurityOperationsService::class);
    expect($service->integrity('config', 'contents')->status)->toBe('verified')->and($service->provenance('demo', '1.0', 'https://example.com')->status)->toBe('verified')->and($service->advisory('Update required', 'high')->status)->toBe('high');
});
it('rejects unsafe provenance and severities', function (): void {
    $service = app(SecurityOperationsService::class);
    expect(fn () => $service->provenance('demo', '1', 'not-a-url'))->toThrow(ValidationException::class)->and(fn () => $service->advisory('Bad', 'urgent'))->toThrow(ValidationException::class);
});
