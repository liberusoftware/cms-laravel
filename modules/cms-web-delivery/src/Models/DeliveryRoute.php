<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property string $path
 * @property string $route_type
 * @property string|null $content_type
 * @property string|null $content_id
 * @property string|null $body
 * @property string|null $canonical_url
 * @property string|null $redirect_url
 * @property int|null $redirect_status
 * @property array<string, mixed>|null $metadata
 * @property array<int, string>|null $cache_tags
 * @property int $cache_ttl
 * @property bool $preview_enabled
 * @property string|null $preview_token_hash
 * @property-read Carbon|null $preview_expires_at
 * @property bool $maintenance
 * @property string $status
 * @property int|null $error_status
 * @property string|null $error_message
 * @property int|null $team_id
 */
final class DeliveryRoute extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_delivery_routes';

    #[\Override]
    protected $fillable = ['path', 'route_type', 'content_type', 'content_id', 'body', 'canonical_url', 'redirect_url', 'redirect_status', 'metadata', 'cache_tags', 'cache_ttl', 'preview_enabled', 'preview_token_hash', 'preview_expires_at', 'maintenance', 'status', 'error_status', 'error_message', 'team_id'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'cache_tags' => 'array', 'preview_expires_at' => 'datetime', 'preview_enabled' => 'boolean', 'maintenance' => 'boolean'];
    }
}
