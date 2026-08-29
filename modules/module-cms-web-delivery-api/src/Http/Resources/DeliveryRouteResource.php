<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDeliveryApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\WebDelivery\Models\DeliveryRoute;

final class DeliveryRouteResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        $route = $this->resource;
        if (! $route instanceof DeliveryRoute) {
            throw new \UnexpectedValueException('Expected a delivery route resource.');
        }

        $routeId = $route->getKey();

        return ['id' => is_scalar($routeId) ? (string) $routeId : '', 'type' => 'cms-delivery-route', 'path' => $route->path, 'route_type' => $route->route_type, 'content_type' => $route->content_type, 'content_id' => $route->content_id, 'canonical_url' => $route->canonical_url, 'redirect_url' => $route->redirect_url, 'redirect_status' => $route->redirect_status, 'metadata' => $route->metadata, 'cache_tags' => $route->cache_tags, 'cache_ttl' => $route->cache_ttl, 'preview_enabled' => $route->preview_enabled, 'maintenance' => $route->maintenance, 'status' => $route->status];
    }
}
