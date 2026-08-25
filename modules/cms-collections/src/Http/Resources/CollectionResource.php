<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'description' => $this->description,
            'schema' => $this->schema,
            'items' => CollectionItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
