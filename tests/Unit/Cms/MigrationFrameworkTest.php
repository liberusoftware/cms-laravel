<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\MigrationFramework\Services\MigrationFrameworkService;

uses(RefreshDatabase::class);

it('supports idempotent resumable migration records', function (): void {
    $service = app(MigrationFrameworkService::class);
    $job = $service->start('wordpress', teamId: 10);
    $record = $service->add($job, 'post', '1', ['title' => 'Hello']);
    $service->add($job, 'post', '1', ['title' => 'Updated']);
    $service->process($record, true);

    expect($job->refresh()->total_records)->toBe(1)
        ->and($job->processed_records)->toBe(1)
        ->and($service->complete($job->refresh())->status)->toBe('completed');
});

it('rejects invalid sources and failed records without reasons', function (): void {
    $service = app(MigrationFrameworkService::class);

    expect(fn () => $service->start('bad source'))->toThrow(ValidationException::class);
    $job = $service->start('joomla');
    $record = $service->add($job, 'page', '1');
    expect(fn () => $service->process($record, false))->toThrow(ValidationException::class);
});
