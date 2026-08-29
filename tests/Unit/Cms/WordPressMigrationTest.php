<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\WordPressMigration\Queries\WordPressMigrationQuery;
use Liberu\Cms\WordPressMigration\Services\WordPressMigrationService;

uses(RefreshDatabase::class);

it('supports resumable typed records and completion with errors', function (): void {
    $service = app(WordPressMigrationService::class);
    $migration = $service->start('https://example.test', ['preserve_ids' => true]);
    $record = $service->addRecord($migration, 'post', 'wp-1', ['title' => 'Hello'], ['guid' => 'https://example.test/?p=1']);
    $service->processRecord($record, true);
    $failed = $service->addRecord($migration->refresh(), 'media', 'wp-2', ['url' => 'https://example.test/image.jpg']);
    $service->processRecord($failed, false, 'Download failed');

    expect($service->complete($migration->refresh())->status)->toBe('completed_with_errors')
        ->and($migration->refresh()->processed_records)->toBe(1);

    expect(fn () => $service->addRecord($migration->refresh(), 'unknown', 'x'))->toThrow(ValidationException::class);
});

it('keeps processed and failed counters correct when records are retried', function (): void {
    $service = app(WordPressMigrationService::class);
    $migration = $service->start();
    $record = $service->addRecord($migration, 'post', 'wp-1');

    $service->processRecord($record, false, 'Temporary failure');
    expect($migration->refresh()->processed_records)->toBe(0)
        ->and($migration->failed_records)->toBe(1);

    $service->processRecord($record->refresh(), true);
    expect($migration->refresh()->processed_records)->toBe(1)
        ->and($migration->failed_records)->toBe(0);

    $service->processRecord($record->refresh(), true);
    expect($migration->refresh()->processed_records)->toBe(1)
        ->and($migration->failed_records)->toBe(0);
});

it('does not complete a migration while records remain pending', function (): void {
    $service = app(WordPressMigrationService::class);
    $migration = $service->start();
    $service->addRecord($migration, 'page', 'wp-2');

    expect(fn () => $service->complete($migration->refresh()))
        ->toThrow(ValidationException::class);
});

it('resolves migrations by public id within the requested tenant', function (): void {
    $service = app(WordPressMigrationService::class);
    $migration = $service->start(teamId: 10);

    expect(app(WordPressMigrationQuery::class)->migrationByPublicId($migration->public_id, 10)?->is($migration))->toBeTrue()
        ->and(app(WordPressMigrationQuery::class)->migrationByPublicId($migration->public_id, 20))->toBeNull();
});
