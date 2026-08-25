<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CollectionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'data' => $this->data,
            'metadata' => $this->metadata,
            'status' => $this->status,
            'published_at' => $this->published_at?->toAtomString(),
            'collection' => new CollectionResource($this->whenLoaded('collection')),
        ];
    }
}
