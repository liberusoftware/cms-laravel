<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\ContentTypes\Models\ContentEntry;

/** Public published-entity read boundary shared by API and Livewire. */
final class PublishedEntityQuery
{
    public function find(string $type, string $slug): ?ContentEntry
    {
        return ContentEntry::query()
            ->with(['type', 'relatedEntries'])
            ->whereRelation('type', 'key', $type)
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->first();
    }

    public function forType(string $type, int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        $term = trim($search);

        return ContentEntry::query()
            ->whereRelation('type', 'key', $type)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->when($term !== '', fn ($query) => $query->where('title', 'like', '%'.addcslashes($term, '%_\\').'%'))
            ->latest('published_at')
            ->paginate(max(1, min($perPage, $this->maxPerPage())));
    }

    private function maxPerPage(): int
    {
        $max = config('cms-api.pagination.max', 100);

        return is_numeric($max) ? max(1, (int) $max) : 100;
    }
}
