<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformance\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class CacheInvalidation extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_cache_invalidations';

    #[\Override]
    protected $fillable = ['team_id', 'idempotency_key', 'tags', 'cache_keys', 'status', 'invalidated_count', 'failure_reason', 'completed_at'];

    protected function casts(): array
    {
        return ['tags' => 'array', 'cache_keys' => 'array', 'invalidated_count' => 'integer', 'completed_at' => 'datetime'];
    }
}
