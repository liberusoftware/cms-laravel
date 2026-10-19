<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBase\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Liberu\Cms\KnowledgeBase\Models\KnowledgeArticle;

final class KnowledgeBaseQuery
{
    /** @return LengthAwarePaginator<int, KnowledgeArticle> */
    public function paginate(int $perPage = 15, string $search = '', bool $publishedOnly = true): LengthAwarePaginator
    {
        $term = trim($search);

        return KnowledgeArticle::query()
            ->when($publishedOnly, fn ($query) => $query->where('status', 'published'))
            ->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('title', 'like', "%{$term}%")->orWhere('slug', 'like', "%{$term}%")))
            ->orderByDesc('search_weight')
            ->latest()
            ->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $key, bool $publishedOnly = true): ?KnowledgeArticle
    {
        return KnowledgeArticle::query()
            ->when($publishedOnly, fn ($query) => $query->where('status', 'published'))
            ->where(fn ($query) => $query->where('public_id', $key)->orWhere('slug', $key))
            ->first();
    }
}
