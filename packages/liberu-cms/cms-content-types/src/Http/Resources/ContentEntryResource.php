<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;

/**
 * The Delivery API wire shape for a Content-Entry: its type key, title, slug,
 * and its typed fields as structured JSON, so a consumer can render custom
 * schemas it does not know at build time.
 *
 * @mixin ContentEntry
 */
final class ContentEntryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = $this->type;

        return [
            'id' => $this->id,
            'type' => $type instanceof ContentType ? $type->key : null,
            'title' => $this->title,
            'slug' => $this->slug,
            'fields' => $this->data ?? [],
            'published_at' => $this->publishedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
