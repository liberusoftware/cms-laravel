<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDeliveryApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class DeliveryRouteResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-delivery-route', 'path' => $this->resource->path, 'route_type' => $this->resource->route_type, 'content_type' => $this->resource->content_type, 'content_id' => $this->resource->content_id, 'canonical_url' => $this->resource->canonical_url, 'redirect_url' => $this->resource->redirect_url, 'redirect_status' => $this->resource->redirect_status, 'metadata' => $this->resource->metadata, 'cache_tags' => $this->resource->cache_tags, 'cache_ttl' => $this->resource->cache_ttl, 'preview_enabled' => $this->resource->preview_enabled, 'maintenance' => $this->resource->maintenance, 'status' => $this->resource->status];
    }
}
