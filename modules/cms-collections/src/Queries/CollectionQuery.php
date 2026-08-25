<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\Collections\Models\CollectionItem;

final class CollectionQuery
{
    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        $term = trim($search);

        return Collection::query()->with(['items' => fn ($query) => $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())])->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$term}%")->orWhere('slug', 'like', "%{$term}%")))->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function published(string $slug, int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        $collection = Collection::query()->where('slug', $slug)->firstOrFail();
        $term = trim($search);

        return $collection->items()->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('title', 'like', "%{$term}%")->orWhere('excerpt', 'like', "%{$term}%")))->latest('published_at')->paginate(max(1, min(100, $perPage)));
    }

    public function publishedCollection(string $slug): ?Collection
    {
        return Collection::query()->where('slug', $slug)->with(['items' => fn ($query) => $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())])->first();
    }

    public function item(string $collection, string $slug): ?CollectionItem
    {
        return CollectionItem::query()->whereHas('collection', fn ($query) => $query->where('slug', $collection))->where('slug', $slug)->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())->with('collection')->first();
    }
}
