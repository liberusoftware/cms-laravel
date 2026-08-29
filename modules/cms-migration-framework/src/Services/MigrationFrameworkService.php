<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFramework\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\MigrationFramework\Models\MigrationJob;
use Liberu\Cms\MigrationFramework\Models\MigrationRecord;

final class MigrationFrameworkService
{
    public function start(string $source, array $options = [], ?int $teamId = null): MigrationJob
    {
        if (preg_match('/\\A[a-zA-Z][a-zA-Z0-9_.-]{0,79}\\z/', $source) !== 1) {
            throw ValidationException::withMessages(['source' => 'Migration source is invalid.']);
        }

        return MigrationJob::query()->create(['public_id' => (string) Str::uuid(), 'source' => $source, 'status' => 'running', 'options' => $options, 'started_at' => now(), 'team_id' => $teamId]);
    }

    public function add(MigrationJob $job, string $recordType, string $sourceId, array $payload = []): MigrationRecord
    {
        if ($job->status !== 'running' || trim($recordType) === '' || trim($sourceId) === '') {
            throw ValidationException::withMessages(['record' => 'Only active jobs accept typed records with source identifiers.']);
        }

        return DB::transaction(function () use ($job, $recordType, $sourceId, $payload): MigrationRecord {
            $record = MigrationRecord::query()->updateOrCreate(['job_id' => $job->id, 'record_type' => $recordType, 'source_id' => $sourceId], ['status' => 'pending', 'payload' => $payload, 'failure_reason' => null, 'team_id' => $job->team_id]);
            if ($record->wasRecentlyCreated) {
                $job->increment('total_records');
            }

            return $record->refresh();
        });
    }

    public function process(MigrationRecord $record, bool $success, ?string $failureReason = null): MigrationRecord
    {
        if (! $success && trim((string) $failureReason) === '') {
            throw ValidationException::withMessages(['failure_reason' => 'Failed records require a reason.']);
        }

        return DB::transaction(function () use ($record, $success, $failureReason): MigrationRecord {
            $job = $record->job()->lockForUpdate()->firstOrFail();
            $next = $success ? 'processed' : 'failed';
            $previous = $record->status;
            $record->update(['status' => $next, 'failure_reason' => $success ? null : $failureReason, 'processed_at' => $success ? now() : null]);
            if ($previous !== $next) {
                if ($previous === 'processed') {
                    $job->decrement('processed_records');
                }
                if ($previous === 'failed') {
                    $job->decrement('failed_records');
                }
                $job->increment($success ? 'processed_records' : 'failed_records');
            }

            return $record->refresh();
        });
    }

    public function complete(MigrationJob $job): MigrationJob
    {
        if (! in_array($job->status, ['draft', 'running'], true) || $job->records()->where('status', 'pending')->exists()) {
            throw ValidationException::withMessages(['job' => 'Only active jobs with no pending records can be completed.']);
        }
        $job->update(['status' => $job->failed_records > 0 ? 'completed_with_errors' : 'completed', 'completed_at' => now()]);

        return $job->refresh();
    }
}
