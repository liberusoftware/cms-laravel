<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ThemeMarketplace\Services\ThemeMarketplaceService;

final class MarketplaceThemeResource extends JsonResource
{
    public function toArray($request): array
    {
        $service = app(ThemeMarketplaceService::class);

        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-marketplace-theme', 'key' => $this->resource->key, 'name' => $this->resource->name, 'version' => $this->resource->version, 'author' => $this->resource->author, 'description' => $this->resource->description, 'manifest' => $this->resource->manifest, 'compatibility' => $this->resource->compatibility, 'preview_url' => $this->resource->preview_url, 'license' => $this->resource->license, 'parent_key' => $this->resource->parent_key, 'status' => $this->resource->status, 'security_status' => $this->resource->security_status, 'rating' => $service->ratingSummary($this->resource), 'created_at' => $this->resource->created_at?->toISOString(), 'updated_at' => $this->resource->updated_at?->toISOString()];
    }
}
