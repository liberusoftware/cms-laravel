<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Queries;

use Illuminate\Support\Collection;
use Liberu\Cms\Pages\Models\Page;

/** Public read boundary for the hierarchical Pages navigation tree. */
final class PageTreeQuery
{
    /**
     * Return an arbitrarily deep tree. Building it from one tenant-scoped
     * query avoids the two-level eager-loading limit of presentation code.
     *
     * @return Collection<int, Page>
     */
    public function roots(string $search = ''): Collection
    {
        $pages = Page::query()->orderBy('title')->get();
        $children = $pages->groupBy(fn (Page $page): string => (string) ($page->parent_id ?? 0));
        $term = mb_strtolower(trim($search));

        $build = function (int $parentId) use (&$build, $children, $term): Collection {
            return $children->get((string) $parentId, collect())
                ->map(function (Page $page) use (&$build): Page {
                    $page->setRelation('children', $build((int) $page->getKey()));

                    return $page;
                })
                ->filter(function (Page $page) use ($term): bool {
                    if ($term === '') {
                        return true;
                    }

                    return str_contains(mb_strtolower($page->title), $term)
                        || $page->children->isNotEmpty();
                })
                ->values();
        };

        return $build(0);
    }
}
