<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestore\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\BackupAndRestore\Models\BackupArtifact;
use Liberu\Cms\BackupAndRestore\Models\BackupSchedule;

final readonly class BackupAndRestoreService
{
    private const array TYPES = ['content', 'configuration', 'database', 'files', 'full'];

    /** @param array<string, mixed> $data */
    public function createArtifact(?int $teamId, array $data): BackupArtifact
    {
        $type = $data['artifact_type'] ?? 'full';
        $path = is_string($data['path'] ?? null) ? $data['path'] : '';
        if (! in_array($type, self::TYPES, true)) {
            throw ValidationException::withMessages(['artifact_type' => 'The backup type is invalid.']);
        }
        if ($path === '' || str_starts_with($path, '/') || str_contains($path, '..')) {
            throw ValidationException::withMessages(['path' => 'The backup path must be relative and safe.']);
        }
        $retention = $this->retention($data['retention_days'] ?? null);

        $configuredDisk = config('backup-and-restore.default_disk', 'local');
        $disk = is_string($data['disk'] ?? null) ? $data['disk'] : (is_string($configuredDisk) ? $configuredDisk : 'local');

        return BackupArtifact::query()->create(['team_id' => $teamId, 'name' => is_string($data['name'] ?? null) ? trim($data['name']) : $type.' backup', 'artifact_type' => $type, 'status' => 'available', 'disk' => $disk, 'path' => $path, 'size' => is_int($data['size'] ?? null) ? $data['size'] : 0, 'checksum' => is_string($data['checksum'] ?? null) ? $data['checksum'] : null, 'encrypted' => is_bool($data['encrypted'] ?? null) ? $data['encrypted'] : false, 'metadata' => is_array($data['metadata'] ?? null) ? $data['metadata'] : null, 'expires_at' => now()->addDays($retention)]);
    }

    public function verify(BackupArtifact $artifact): BackupArtifact
    {
        if (! $artifact->checksum) {
            throw ValidationException::withMessages(['checksum' => 'A checksum is required before verification.']);
        }
        $checksum = $artifact->checksum;
        $disk = Storage::disk($artifact->disk);
        if (! $disk->exists($artifact->path)) {
            return $this->markVerification($artifact, false);
        }
        $actual = hash('sha256', (string) $disk->get($artifact->path));

        return $this->markVerification($artifact, hash_equals(strtolower($checksum), $actual));
    }

    /** @return array{artifact_id:mixed,artifact_type:string,encrypted:bool,path:string,metadata:array<mixed>,requires_confirmation:bool} */
    public function restorePreview(BackupArtifact $artifact): array
    {
        if (! in_array($artifact->status, ['available', 'verified'], true) || $artifact->expires_at?->isPast()) {
            throw ValidationException::withMessages(['artifact' => 'Only an available, unexpired backup can be restored.']);
        }

        return ['artifact_id' => $artifact->getKey(), 'artifact_type' => $artifact->artifact_type, 'encrypted' => $artifact->encrypted, 'path' => $artifact->path, 'metadata' => $artifact->metadata ?? [], 'requires_confirmation' => true];
    }

    /** @param array<string, mixed> $data */
    public function schedule(?int $teamId, array $data): BackupSchedule
    {
        $frequency = is_string($data['frequency'] ?? null) ? $data['frequency'] : 'daily';
        if (! in_array($frequency, ['hourly', 'daily', 'weekly', 'monthly'], true)) {
            throw ValidationException::withMessages(['frequency' => 'The schedule frequency is invalid.']);
        }

        $types = ['full'];
        if (is_array($data['artifact_types'] ?? null)) {
            $requested = array_values(array_filter($data['artifact_types'], static fn (mixed $item): bool => is_string($item)));
            $types = array_values(array_intersect($requested, self::TYPES));
        }
        $retention = $this->retention($data['retention_days'] ?? null);

        return BackupSchedule::query()->create(['team_id' => $teamId, 'name' => is_string($data['name'] ?? null) ? trim($data['name']) : 'Backup schedule', 'frequency' => $frequency, 'artifact_types' => $types, 'retention_days' => $retention, 'enabled' => is_bool($data['enabled'] ?? null) ? $data['enabled'] : true, 'next_run_at' => $data['next_run_at'] ?? now()]);
    }

    private function markVerification(BackupArtifact $artifact, bool $valid): BackupArtifact
    {
        $artifact->update(['status' => $valid ? 'verified' : 'failed', 'verified_at' => $valid ? now() : null]);

        $fresh = $artifact->fresh();
        if (! $fresh) {
            throw new \RuntimeException('The backup artifact could not be refreshed.');
        }

        return $fresh;
    }

    private function retention(mixed $value): int
    {
        $default = config('backup-and-restore.default_retention_days', 30);
        $maximum = config('backup-and-restore.max_retention_days', 3650);

        return max(1, min(is_int($value) ? $value : (is_int($default) ? $default : 30), is_int($maximum) ? $maximum : 3650));
    }
}
