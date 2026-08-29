<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property string $public_id
 * @property string|null $source_url
 * @property string $status
 * @property int $total_records
 * @property int $processed_records
 * @property int $failed_records
 * @property int|null $team_id
 * @property array<string, mixed>|null $options
 * @property array<string, mixed>|null $metadata
 * @property string|null $failure_reason
 * @property-read Carbon|null $started_at
 * @property-read Carbon|null $completed_at
 */
final class WordPressMigration extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_wordpress_migrations';

    #[\Override]
    protected $fillable = ['public_id', 'source_url', 'status', 'total_records', 'processed_records', 'failed_records', 'options', 'metadata', 'failure_reason', 'started_at', 'completed_at', 'team_id'];

    protected function casts(): array
    {
        return ['options' => 'array', 'metadata' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    /** @return HasMany<WordPressMigrationRecord, $this> */
    public function records(): HasMany
    {
        return $this->hasMany(WordPressMigrationRecord::class, 'migration_id');
    }
}
