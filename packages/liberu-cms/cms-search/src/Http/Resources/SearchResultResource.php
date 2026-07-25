<?php

declare(strict_types=1);

namespace Liberu\Cms\Search\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Contracts\Search\SearchResult;

/**
 * The Delivery API wire shape for a search hit. `type` + `slug` let the consumer
 * build its own link to the underlying content.
 *
 * @mixin SearchResult
 */
final class SearchResultResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'score' => $this->score,
        ];
    }
}
