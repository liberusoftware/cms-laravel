<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFramework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class MigrationRecord extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_migration_records';

    #[\Override]
    protected $fillable = ['job_id', 'record_type', 'source_id', 'status', 'payload', 'failure_reason', 'processed_at', 'team_id'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'processed_at' => 'datetime'];
    }

    /** @return BelongsTo<MigrationJob, $this> */
    public function job(): BelongsTo
    {
        return $this->belongsTo(MigrationJob::class, 'job_id');
    }
}
