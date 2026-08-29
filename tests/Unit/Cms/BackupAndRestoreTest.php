<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\BackupAndRestore\Services\BackupAndRestoreService;

uses(RefreshDatabase::class);

it('creates and verifies a backup artifact using its checksum', function (): void {
    Storage::fake('local');
    Storage::disk('local')->put('backups/site.tar', 'backup');
    $service = app(BackupAndRestoreService::class);
    $artifact = $service->createArtifact(4, ['name' => 'Nightly', 'artifact_type' => 'full', 'path' => 'backups/site.tar', 'checksum' => hash('sha256', 'backup')]);

    expect($service->verify($artifact)->status)->toBe('verified')
        ->and($service->restorePreview($artifact)['requires_confirmation'])->toBeTrue();
});

it('rejects unsafe paths and invalid schedule frequencies', function (): void {
    $service = app(BackupAndRestoreService::class);
    expect(fn () => $service->createArtifact(4, ['path' => '../dump']))->toThrow(ValidationException::class)
        ->and(fn () => $service->schedule(4, ['frequency' => 'every-minute']))->toThrow(ValidationException::class);
});

it('creates a typed schedule with bounded retention', function (): void {
    $schedule = app(BackupAndRestoreService::class)->schedule(4, ['name' => 'Weekly', 'frequency' => 'weekly', 'artifact_types' => ['database'], 'retention_days' => 99999]);
    expect($schedule->artifact_types)->toBe(['database'])->and($schedule->retention_days)->toBe(3650);
});
