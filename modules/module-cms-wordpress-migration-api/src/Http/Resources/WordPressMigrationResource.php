<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigrationApi\Http\Resources;

use Liberu\Cms\WordPressMigration\Models\WordPressMigration;

final class WordPressMigrationResource
{
    /** @return array<string, mixed> */
    public static function make(WordPressMigration $migration): array
    {
        return ['id' => (string) $migration->public_id, 'type' => 'cms-wordpress-migration', 'source_url' => $migration->source_url, 'status' => $migration->status, 'total_records' => $migration->total_records, 'processed_records' => $migration->processed_records, 'failed_records' => $migration->failed_records, 'options' => $migration->options ?? [], 'failure_reason' => $migration->failure_reason, 'started_at' => $migration->started_at?->toISOString(), 'completed_at' => $migration->completed_at?->toISOString()];
    }
}
