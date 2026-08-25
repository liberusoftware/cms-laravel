<?php

declare(strict_types=1);

namespace Liberu\Cms\Taxonomy\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\Taxonomy\Models\Taxonomy;
use Liberu\Cms\Taxonomy\Models\Term;

/** Public tenant-scoped read boundary for taxonomy consumers. */
final class TaxonomyQuery
{
    public function taxonomies(?string $key = null, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $term = trim((string) $search);

        return Taxonomy::query()
            ->withCount('terms')
            ->when($key !== null && $key !== '', fn ($query) => $query->where('key', $key))
            ->when($term !== '', fn ($query) => $query->where(function ($query) use ($term): void {
                $escaped = addcslashes($term, '%_\\');
                $query->where('name', 'like', '%'.$escaped.'%')
                    ->orWhere('key', 'like', '%'.$escaped.'%')
                    ->orWhereHas('terms', fn ($terms) => $terms->where('name', 'like', '%'.$escaped.'%')->orWhere('slug', 'like', '%'.$escaped.'%'));
            }))
            ->orderBy('name')
            ->paginate(max(1, min($perPage, $this->maxPerPage())));
    }

    public function taxonomy(int $id): ?Taxonomy
    {
        return Taxonomy::query()->withCount('terms')->find($id);
    }

    public function term(int $id): ?Term
    {
        return Term::query()->with(['taxonomy', 'parent'])->withCount('assignments')->find($id);
    }

    /** @return array<int, Term> */
    public function terms(int $taxonomyId, ?string $search = null): array
    {
        $term = trim((string) $search);

        return Term::query()
            ->where('taxonomy_id', $taxonomyId)
            ->withCount('assignments')
            ->when($term !== '', function ($query) use ($term): void {
                $escaped = addcslashes($term, '%_\\');
                $query->where(function ($query) use ($escaped): void {
                    $query->where('name', 'like', '%'.$escaped.'%')
                        ->orWhere('slug', 'like', '%'.$escaped.'%');
                });
            })
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->all();
    }

    private function maxPerPage(): int
    {
        $max = config('cms-api.pagination.max', 100);

        return is_numeric($max) ? max(1, (int) $max) : 100;
    }
}
