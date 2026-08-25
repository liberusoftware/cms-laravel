<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class ThemeInstallationResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-theme-installation', 'theme_key' => $this->resource->theme?->key, 'site_key' => $this->resource->site_key, 'installed_version' => $this->resource->installed_version, 'updated_at_version' => $this->resource->updated_at_version, 'status' => $this->resource->status, 'installed_at' => $this->resource->installed_at?->toISOString()];
    }
}
