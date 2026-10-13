<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformance\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property string $cache_key
 * @property string $status
 * @property int $hits
 * @property int $misses
 * @property array<int, string>|null $tags
 */
final class CacheEntry extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_cache_entries';

    #[\Override]
    protected $fillable = ['team_id', 'cache_key', 'cache_type', 'tags', 'status', 'ttl_seconds', 'size_bytes', 'hits', 'misses', 'warmed_at', 'last_invalidated_at', 'metadata'];

    protected function casts(): array
    {
        return ['tags' => 'array', 'ttl_seconds' => 'integer', 'size_bytes' => 'integer', 'hits' => 'integer', 'misses' => 'integer', 'warmed_at' => 'datetime', 'last_invalidated_at' => 'datetime', 'metadata' => 'array'];
    }
}
