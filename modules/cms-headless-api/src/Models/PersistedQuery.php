<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int|null $team_id
 * @property int $id
 * @property string $query_hash
 * @property string $query_body
 * @property Carbon|null $last_used_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class PersistedQuery extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_headless_persisted_queries';

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected $casts = ['team_id' => 'integer', 'last_used_at' => 'datetime'];
}
