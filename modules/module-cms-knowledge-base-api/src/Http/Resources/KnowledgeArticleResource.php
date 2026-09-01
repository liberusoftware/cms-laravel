<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBaseApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\KnowledgeBase\Models\KnowledgeArticle;
use LogicException;

final class KnowledgeArticleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof KnowledgeArticle) {
            throw new LogicException('KnowledgeArticleResource requires a KnowledgeArticle instance.');
        }

        $article = $this->resource;

        return ['id' => (string) $article->public_id, 'type' => 'cms-knowledge-base-articles', 'slug' => $article->slug, 'title' => $article->title, 'body' => $article->body, 'status' => $article->status, 'parent_id' => $article->parent_id, 'search_weight' => $article->search_weight, 'published_at' => $article->published_at?->toISOString(), 'review_due_at' => $article->review_due_at?->toISOString(), 'created_at' => $article->created_at?->toISOString(), 'updated_at' => $article->updated_at?->toISOString()];
    }
}
