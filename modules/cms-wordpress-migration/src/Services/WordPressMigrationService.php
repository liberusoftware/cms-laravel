<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigration\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\WordPressMigration\Models\WordPressMigration;
use Liberu\Cms\WordPressMigration\Models\WordPressMigrationRecord;

final class WordPressMigrationService
{
    private const array TYPES = ['post', 'page', 'custom_type', 'taxonomy', 'term', 'metadata', 'user', 'author', 'comment', 'media', 'menu', 'redirect'];

    /** @param array<string, mixed> $options */
    public function start(?string $sourceUrl = null, array $options = [], ?int $teamId = null): WordPressMigration
    {
        if ($sourceUrl !== null && filter_var($sourceUrl, FILTER_VALIDATE_URL) === false) {
            throw ValidationException::withMessages(['source_url' => 'Source URL must be valid.']);
        }

        return WordPressMigration::query()->create(['public_id' => (string) Str::uuid(), 'source_url' => $sourceUrl, 'status' => 'running', 'options' => $options, 'team_id' => $teamId, 'started_at' => now()]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $sourceIdentifiers
     */
    public function addRecord(WordPressMigration $migration, string $recordType, string $sourceId, array $payload = [], array $sourceIdentifiers = [], ?string $sourceParentId = null): WordPressMigrationRecord
    {
        if (! in_array($recordType, self::TYPES, true) || trim($sourceId) === '') {
            throw ValidationException::withMessages(['record_type' => 'Unsupported record type or missing source identifier.']);
        }
        if (! in_array($migration->status, ['draft', 'running'], true)) {
            throw ValidationException::withMessages(['migration' => 'Records cannot be added after migration completion.']);
        }

        return DB::transaction(function () use ($migration, $recordType, $sourceId, $payload, $sourceIdentifiers, $sourceParentId): WordPressMigrationRecord {
            $record = WordPressMigrationRecord::query()->updateOrCreate(['migration_id' => $migration->id, 'record_type' => $recordType, 'source_id' => $sourceId], ['source_parent_id' => $sourceParentId, 'status' => 'pending', 'payload' => $payload, 'source_identifiers' => $sourceIdentifiers, 'failure_reason' => null, 'team_id' => $migration->team_id]);
            $migration->increment('total_records', $record->wasRecentlyCreated ? 1 : 0);

            return $record->refresh();
        });
    }

    public function processRecord(WordPressMigrationRecord $record, bool $success = true, ?string $failureReason = null): WordPressMigrationRecord
    {
        if (! $success && trim((string) $failureReason) === '') {
            throw ValidationException::withMessages(['failure_reason' => 'A failed record requires a reason.']);
        }

        return DB::transaction(function () use ($record, $success, $failureReason): WordPressMigrationRecord {
            $migration = $record->migration()->lockForUpdate()->firstOrFail();
            $nextStatus = $success ? 'processed' : 'failed';
            $previousStatus = $record->status;

            $record->update(['status' => $nextStatus, 'failure_reason' => $success ? null : $failureReason, 'processed_at' => $success ? now() : null]);

            if ($previousStatus !== $nextStatus) {
                $counts = [
                    'processed_records' => max(0, (int) $migration->processed_records),
                    'failed_records' => max(0, (int) $migration->failed_records),
                ];
                if ($previousStatus === 'processed') {
                    $counts['processed_records']--;
                } elseif ($previousStatus === 'failed') {
                    $counts['failed_records']--;
                }
                if ($nextStatus === 'processed') {
                    $counts['processed_records']++;
                } else {
                    $counts['failed_records']++;
                }
                $migration->forceFill($counts)->save();
            }

            return $record->refresh();
        });
    }

    public function complete(WordPressMigration $migration): WordPressMigration
    {
        if (! in_array($migration->status, ['running', 'draft'], true)) {
            throw ValidationException::withMessages(['migration' => 'Only an active migration can be completed.']);
        }

        if ($migration->records()->whereNotIn('status', ['processed', 'failed'])->exists()) {
            throw ValidationException::withMessages(['migration' => 'All migration records must be processed before completion.']);
        }

        $migration->update(['status' => $migration->failed_records > 0 ? 'completed_with_errors' : 'completed', 'completed_at' => now()]);

        return $migration->refresh();
    }

    public function fail(WordPressMigration $migration, string $reason): WordPressMigration
    {
        if (trim($reason) === '') {
            throw ValidationException::withMessages(['failure_reason' => 'Failure reason is required.']);
        }
        $migration->update(['status' => 'failed', 'failure_reason' => $reason, 'completed_at' => now()]);

        return $migration->refresh();
    }
}
