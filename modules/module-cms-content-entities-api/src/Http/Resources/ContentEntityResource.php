<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;

/** @mixin ContentEntry */
final class ContentEntityResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var ContentEntry $entry */
        $entry = $this->resource;
        $type = $entry->type;

        return [
            'id' => (string) $entry->id,
            'type' => $type instanceof ContentType ? $type->key : 'cms-content-entities',
            'attributes' => [
                'title' => $entry->title,
                'slug' => $entry->slug,
                'canonical_id' => $entry->canonical_id,
                'author_id' => $entry->author_id === null ? null : (string) $entry->author_id,
                'fields' => $entry->data ?? [],
                'status' => $entry->workflowState()->value,
                'published_at' => $entry->publishedAt()?->format(\DateTimeInterface::ATOM),
            ],
        ];
    }
}
