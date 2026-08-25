<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class DeliveryRoute extends Model
{
    use HasTenant;

    protected $table = 'cms_delivery_routes';
    protected $fillable = ['path', 'route_type', 'content_type', 'content_id', 'body', 'canonical_url', 'redirect_url', 'redirect_status', 'metadata', 'cache_tags', 'cache_ttl', 'preview_enabled', 'preview_token_hash', 'preview_expires_at', 'maintenance', 'status', 'error_status', 'error_message', 'team_id'];
    protected function casts(): array { return ['metadata' => 'array', 'cache_tags' => 'array', 'preview_expires_at' => 'datetime', 'preview_enabled' => 'boolean', 'maintenance' => 'boolean']; }
}
