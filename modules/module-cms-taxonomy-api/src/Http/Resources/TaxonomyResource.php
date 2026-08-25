<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class TaxonomyResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-taxonomy', 'key' => $this->resource->key, 'name' => $this->resource->name, 'description' => $this->resource->description, 'hierarchical' => $this->resource->hierarchical, 'exclusive' => $this->resource->exclusive, 'terms_count' => $this->resource->terms_count ?? $this->resource->terms()->count(), 'created_at' => $this->resource->created_at?->toISOString(), 'updated_at' => $this->resource->updated_at?->toISOString()];
    }
}
