<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContentApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\EditorialContent\Models\EditorialPost;
use LogicException;

final class EditorialPostResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof EditorialPost) {
            throw new LogicException('EditorialPostResource requires an EditorialPost instance.');
        }

        $post = $this->resource;

        return ['id' => (string) $post->public_id, 'type' => 'cms-editorial-posts', 'slug' => $post->slug, 'title' => $post->title, 'excerpt' => $post->excerpt, 'body' => $post->body, 'status' => $post->status, 'featured' => (bool) $post->featured, 'author_id' => $post->author_id, 'series_id' => $post->series_id, 'categories' => $post->categories ?? [], 'tags' => $post->tags ?? [], 'related_public_ids' => $post->related_public_ids ?? [], 'published_at' => $post->published_at?->toISOString(), 'archived_at' => $post->archived_at?->toISOString(), 'created_at' => $post->created_at?->toISOString(), 'updated_at' => $post->updated_at?->toISOString()];
    }
}
