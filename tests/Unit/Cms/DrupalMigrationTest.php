<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\DrupalMigration\Services\DrupalMigrationService;

uses(RefreshDatabase::class);

it('adapts Drupal records to the resumable migration framework', function (): void {
    $service = app(DrupalMigrationService::class);
    $job = $service->start('https://drupal.example.test', teamId: 10);
    $record = $service->add($job, 'node', '42', ['title' => 'Welcome']);
    $service->process($record, true);

    expect($job->refresh()->source)->toBe('drupal')
        ->and($job->processed_records)->toBe(1)
        ->and($service->complete($job->refresh())->status)->toBe('completed');
});
