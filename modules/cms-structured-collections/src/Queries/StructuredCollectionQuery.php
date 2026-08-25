<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollections\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\Collections\Models\CollectionItem;
use Liberu\Cms\Collections\Queries\CollectionQuery;

/** Stable canonical read boundary for structured collections. */
final class StructuredCollectionQuery
{
    public function __construct(private readonly CollectionQuery $legacy) {}

    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return $this->legacy->paginate($perPage, $search);
    }

    public function collection(string $slug): ?Collection
    {
        return $this->legacy->publishedCollection($slug);
    }

    public function records(string $slug, int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return $this->legacy->published($slug, $perPage, $search);
    }

    public function record(string $collection, string $slug): ?CollectionItem
    {
        return $this->legacy->item($collection, $slug);
    }
}
