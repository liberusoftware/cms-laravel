<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\FormOperations\Services\FormOperationsService;

uses(RefreshDatabase::class);

it('requires consent and stores encrypted tenant-scoped submissions that can be exported', function (): void {
    $service = app(FormOperationsService::class);
    $submission = $service->submit(4, ['email' => 'person@example.test'], 'client-a', true, 7);

    expect($submission->encrypted_payload)->not->toContain('person@example.test')
        ->and($service->export($submission, 7))->toBe(['email' => 'person@example.test']);
});

it('rejects missing consent and cross-tenant export', function (): void {
    $service = app(FormOperationsService::class);
    expect(fn () => $service->submit(4, [], 'client-a', false, 7))->toThrow(ValidationException::class);
    $submission = $service->submit(4, [], 'client-b', true, 7);
    expect(fn () => $service->export($submission, 8))->toThrow(ValidationException::class);
});
