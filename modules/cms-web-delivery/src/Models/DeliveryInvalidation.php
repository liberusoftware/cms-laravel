<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property string $idempotency_key
 * @property array<int, string> $cache_tags
 * @property string $status
 * @property string|null $provider
 * @property string|null $failure_reason
 * @property-read Carbon|null $completed_at
 * @property int|null $team_id
 */
final class DeliveryInvalidation extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_delivery_invalidations';

    #[\Override]
    protected $fillable = ['idempotency_key', 'cache_tags', 'status', 'provider', 'failure_reason', 'completed_at', 'team_id'];

    protected function casts(): array
    {
        return ['cache_tags' => 'array', 'completed_at' => 'datetime'];
    }
}
