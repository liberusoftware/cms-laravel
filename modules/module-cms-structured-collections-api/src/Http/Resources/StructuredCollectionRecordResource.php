<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class StructuredCollectionRecordResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-structured-collection-record', 'collection_id' => (string) $this->resource->collection_id, 'title' => $this->resource->title, 'slug' => $this->resource->slug, 'content' => $this->resource->content, 'excerpt' => $this->resource->excerpt, 'data' => $this->resource->data, 'metadata' => $this->resource->metadata, 'status' => $this->resource->status, 'published_at' => $this->resource->published_at?->toISOString(), 'collection' => new StructuredCollectionResource($this->whenLoaded('collection'))];
    }
}
