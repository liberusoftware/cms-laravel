<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestore\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\BackupAndRestore\Models\BackupArtifact;
use Liberu\Cms\BackupAndRestore\Models\BackupSchedule;

final readonly class BackupAndRestoreService
{
    private const TYPES = ['content', 'configuration', 'database', 'files', 'full'];

    public function createArtifact(?int $teamId, array $data): BackupArtifact
    {
        $type = $data['artifact_type'] ?? 'full';
        $path = (string) ($data['path'] ?? '');
        if (! in_array($type, self::TYPES, true)) {
            throw ValidationException::withMessages(['artifact_type' => 'The backup type is invalid.']);
        }
        if ($path === '' || str_starts_with($path, '/') || str_contains($path, '..')) {
            throw ValidationException::withMessages(['path' => 'The backup path must be relative and safe.']);
        }
        $retention = max(1, min((int) ($data['retention_days'] ?? config('backup-and-restore.default_retention_days')), (int) config('backup-and-restore.max_retention_days')));

        return BackupArtifact::query()->create(['team_id' => $teamId, 'name' => trim((string) ($data['name'] ?? $type.' backup')), 'artifact_type' => $type, 'status' => 'available', 'disk' => $data['disk'] ?? config('backup-and-restore.default_disk'), 'path' => $path, 'size' => (int) ($data['size'] ?? 0), 'checksum' => $data['checksum'] ?? null, 'encrypted' => (bool) ($data['encrypted'] ?? false), 'metadata' => $data['metadata'] ?? null, 'expires_at' => now()->addDays($retention)]);
    }

    public function verify(BackupArtifact $artifact): BackupArtifact
    {
        if (! $artifact->checksum) {
            throw ValidationException::withMessages(['checksum' => 'A checksum is required before verification.']);
        }
        $disk = Storage::disk($artifact->disk);
        if (! $disk->exists($artifact->path)) {
            return $this->markVerification($artifact, false);
        }
        $actual = hash('sha256', $disk->get($artifact->path));

        return $this->markVerification($artifact, hash_equals(strtolower($artifact->checksum), $actual));
    }

    public function restorePreview(BackupArtifact $artifact): array
    {
        if (! in_array($artifact->status, ['available', 'verified'], true) || $artifact->expires_at?->isPast()) {
            throw ValidationException::withMessages(['artifact' => 'Only an available, unexpired backup can be restored.']);
        }

        return ['artifact_id' => $artifact->getKey(), 'artifact_type' => $artifact->artifact_type, 'encrypted' => $artifact->encrypted, 'path' => $artifact->path, 'metadata' => $artifact->metadata ?? [], 'requires_confirmation' => true];
    }

    public function schedule(?int $teamId, array $data): BackupSchedule
    {
        $frequency = (string) ($data['frequency'] ?? 'daily');
        if (! in_array($frequency, ['hourly', 'daily', 'weekly', 'monthly'], true)) {
            throw ValidationException::withMessages(['frequency' => 'The schedule frequency is invalid.']);
        }

        return BackupSchedule::query()->create(['team_id' => $teamId, 'name' => trim((string) ($data['name'] ?? 'Backup schedule')), 'frequency' => $frequency, 'artifact_types' => array_values(array_intersect($data['artifact_types'] ?? ['full'], self::TYPES)), 'retention_days' => max(1, min((int) ($data['retention_days'] ?? config('backup-and-restore.default_retention_days')), (int) config('backup-and-restore.max_retention_days'))), 'enabled' => (bool) ($data['enabled'] ?? true), 'next_run_at' => $data['next_run_at'] ?? now()]);
    }

    private function markVerification(BackupArtifact $artifact, bool $valid): BackupArtifact
    {
        $artifact->update(['status' => $valid ? 'verified' : 'failed', 'verified_at' => $valid ? now() : null]);

        return $artifact->fresh();
    }
}
