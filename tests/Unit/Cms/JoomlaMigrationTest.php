<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\JoomlaMigration\Services\JoomlaMigrationService;

uses(RefreshDatabase::class);

it('adapts Joomla records to the resumable migration framework', function (): void {
    $service = app(JoomlaMigrationService::class);
    $job = $service->start('https://joomla.example.test', teamId: 10);
    $record = $service->add($job, 'article', '42', ['title' => 'Welcome']);
    $service->process($record, true);

    expect($job->refresh()->source)->toBe('joomla')
        ->and($job->processed_records)->toBe(1)
        ->and($service->complete($job->refresh())->status)->toBe('completed');
});
