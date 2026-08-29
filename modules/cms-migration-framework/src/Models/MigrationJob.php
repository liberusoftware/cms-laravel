<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFramework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class MigrationJob extends Model
{
    use HasTenant;

    protected $table = 'cms_migration_jobs';

    protected $fillable = ['public_id', 'source', 'status', 'total_records', 'processed_records', 'failed_records', 'options', 'failure_reason', 'started_at', 'completed_at', 'team_id'];

    protected function casts(): array
    {
        return ['options' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    /** @return HasMany<MigrationRecord, $this> */
    public function records(): HasMany
    {
        return $this->hasMany(MigrationRecord::class, 'job_id');
    }
}
