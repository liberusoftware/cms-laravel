<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
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
