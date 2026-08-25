<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class DeliveryInvalidation extends Model
{
    use HasTenant;

    protected $table = 'cms_delivery_invalidations';

    protected $fillable = ['idempotency_key', 'cache_tags', 'status', 'provider', 'failure_reason', 'completed_at', 'team_id'];

    protected function casts(): array
    {
        return ['cache_tags' => 'array', 'completed_at' => 'datetime'];
    }
}
