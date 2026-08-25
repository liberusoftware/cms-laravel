<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentIntegrity\Services\ContentIntegrityService;

uses(RefreshDatabase::class);

it('runs a scan, records findings, and produces a report count', function (): void {
    $service = app(ContentIntegrityService::class);
    $scan = $service->startScan(3, 'pages');
    $finding = $service->finding($scan, ['subject_type' => 'page', 'subject_key' => '42', 'kind' => 'broken-link', 'severity' => 'error', 'message' => 'Link is unavailable']);

    expect($finding->team_id)->toBe(3)
        ->and($scan->fresh()->finding_count)->toBe(1)
        ->and($service->completeScan($scan)->status)->toBe('completed');
});

it('validates repair findings and resolves them', function (): void {
    $service = app(ContentIntegrityService::class);
    $scan = $service->startScan(3);

    expect(fn () => $service->finding($scan, ['subject_type' => 'page', 'subject_key' => '42', 'kind' => 'duplicate']))
        ->toThrow(ValidationException::class);

    $finding = $service->finding($scan, ['subject_type' => 'page', 'subject_key' => '42', 'kind' => 'duplicate', 'message' => 'Duplicate content']);
    expect($service->resolve($finding)->status)->toBe('resolved');
});
