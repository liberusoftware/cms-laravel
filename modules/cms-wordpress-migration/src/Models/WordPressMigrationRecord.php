<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property int $migration_id
 * @property string $record_type
 * @property string $source_id
 * @property string|null $source_parent_id
 * @property string $status
 * @property array<string, mixed>|null $payload
 * @property array<string, mixed>|null $source_identifiers
 * @property string|null $failure_reason
 * @property int|null $team_id
 * @property-read Carbon|null $processed_at
 */
final class WordPressMigrationRecord extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_wordpress_migration_records';

    #[\Override]
    protected $fillable = ['migration_id', 'record_type', 'source_id', 'source_parent_id', 'status', 'payload', 'source_identifiers', 'failure_reason', 'processed_at', 'team_id'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'source_identifiers' => 'array', 'processed_at' => 'datetime'];
    }

    /** @return BelongsTo<WordPressMigration, $this> */
    public function migration(): BelongsTo
    {
        return $this->belongsTo(WordPressMigration::class, 'migration_id');
    }
}
