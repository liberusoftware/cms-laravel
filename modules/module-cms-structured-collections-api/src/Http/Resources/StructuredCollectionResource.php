<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class StructuredCollectionResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-structured-collections', 'name' => $this->resource->name, 'slug' => $this->resource->slug, 'collection_type' => $this->resource->type, 'description' => $this->resource->description, 'schema' => $this->resource->schema, 'records' => StructuredCollectionRecordResource::collection($this->whenLoaded('items')), 'created_at' => $this->resource->created_at?->toISOString(), 'updated_at' => $this->resource->updated_at?->toISOString()];
    }
}
