<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class TranslationVendorResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-translation-vendor', 'key' => $this->resource->key, 'driver' => $this->resource->driver, 'name' => $this->resource->name, 'status' => $this->resource->status, 'settings' => $this->resource->settings ?? []];
    }
}
