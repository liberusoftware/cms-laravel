<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigrationApi\Http\Resources;

use Liberu\Cms\WordPressMigration\Models\WordPressMigrationRecord;

final class WordPressMigrationRecordResource
{
    /** @return array<string, mixed> */
    public static function make(WordPressMigrationRecord $record): array
    {
        return ['id' => (string) $record->id, 'type' => 'cms-wordpress-migration-record', 'record_type' => $record->record_type, 'source_id' => $record->source_id, 'source_parent_id' => $record->source_parent_id, 'status' => $record->status, 'payload' => $record->payload ?? [], 'source_identifiers' => $record->source_identifiers ?? [], 'failure_reason' => $record->failure_reason, 'processed_at' => $record->processed_at?->toISOString()];
    }
}
