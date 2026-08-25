<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilder\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\ViewsAndQueryBuilder\Models\ViewDefinition;

final class ViewDefinitionQuery
{
    public function list(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        $term = trim($search);

        return ViewDefinition::query()
            ->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$term}%")->orWhere('slug', 'like', "%{$term}%")))
            ->latest()
            ->paginate($this->perPage($perPage));
    }

    public function find(string $slug): ?ViewDefinition
    {
        return ViewDefinition::query()->where('slug', $slug)->first();
    }

    public function publishedList(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        $term = trim($search);

        return ViewDefinition::query()->published()
            ->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$term}%")->orWhere('slug', 'like', "%{$term}%")))
            ->latest()
            ->paginate($this->perPage($perPage));
    }

    public function findPublished(string $slug): ?ViewDefinition
    {
        return ViewDefinition::query()->published()->where('slug', $slug)->first();
    }

    private function perPage(int $perPage): int
    {
        $max = (int) config('views-and-query-builder.pagination.max', 100);

        return max(1, min($perPage, max(1, $max)));
    }
}
