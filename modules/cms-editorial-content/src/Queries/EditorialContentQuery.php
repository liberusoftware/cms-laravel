<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContent\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Liberu\Cms\EditorialContent\Models\EditorialPost;

final class EditorialContentQuery
{
    /** @return LengthAwarePaginator<int, EditorialPost> */
    public function paginate(int $perPage = 15, string $search = '', bool $includeArchived = false): LengthAwarePaginator
    {
        $term = trim($search);

        return EditorialPost::query()
            ->when(! $includeArchived, fn ($query) => $query->where('status', '!=', 'archived'))
            ->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('title', 'like', "%{$term}%")->orWhere('slug', 'like', "%{$term}%")))
            ->latest()
            ->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $key, bool $publishedOnly = false): ?EditorialPost
    {
        return EditorialPost::query()
            ->when($publishedOnly, fn ($query) => $query->where('status', 'published'))
            ->where(fn ($query) => $query->where('public_id', $key)->orWhere('slug', $key))
            ->first();
    }
}
