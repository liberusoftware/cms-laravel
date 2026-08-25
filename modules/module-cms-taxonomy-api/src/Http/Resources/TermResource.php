<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class TermResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-taxonomy-term', 'taxonomy_id' => (string) $this->resource->taxonomy_id, 'parent_id' => $this->resource->parent_id === null ? null : (string) $this->resource->parent_id, 'slug' => $this->resource->slug, 'name' => $this->resource->name, 'description' => $this->resource->description, 'synonyms' => $this->resource->synonyms ?? [], 'translations' => $this->resource->translations ?? [], 'position' => $this->resource->position, 'assignments_count' => $this->resource->assignments_count ?? $this->resource->assignments()->count()];
    }
}
