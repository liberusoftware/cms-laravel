<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilderApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class ViewDefinitionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->resource->getKey(),
            'type' => 'cms-view-definition',
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
            'source' => $this->resource->source,
            'description' => $this->resource->description,
            'definition' => $this->resource->definition,
            'visibility' => $this->resource->visibility,
            'status' => $this->resource->status,
            'published_at' => $this->resource->published_at?->toISOString(),
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
        ];
    }
}
