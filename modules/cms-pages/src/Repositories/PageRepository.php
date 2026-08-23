<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Core\Database\Concerns\FiltersContentQueries;
use Liberu\Cms\Pages\Contracts\PageRepositoryInterface;
use Liberu\Cms\Pages\Models\Page;

final class PageRepository implements PageRepositoryInterface
{
    use FiltersContentQueries;

    public function find(int $id): ?Page
    {
        return $this->filterContentQuery('pages.find', Page::query()->whereKey($id))->first();
    }

    public function findBySlug(string $slug): ?Page
    {
        return $this->filterContentQuery('pages.find_by_slug', Page::query()->where('slug', $slug))->first();
    }

    public function published(): array
    {
        return $this->filterContentQuery('pages.published', Page::query()
            ->where('status', WorkflowState::Published->value)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at'))
            ->get()
            ->all();
    }

    public function roots(): array
    {
        return $this->filterContentQuery('pages.roots', Page::query()
            ->whereNull('parent_id')
            ->orderBy('title'))
            ->get()
            ->all();
    }

    public function search(string $query, int $limit): array
    {
        $like = '%'.addcslashes($query, '%_\\').'%';

        return $this->filterContentQuery('pages.search', Page::query()
            ->where('status', WorkflowState::Published->value)
            ->where(function (Builder $inner): void {
                $inner->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->where(function (Builder $inner) use ($like): void {
                $inner->where('title', 'like', $like)
                    ->orWhere('content', 'like', $like)
                    ->orWhere('excerpt', 'like', $like);
            })
            ->limit($limit))
            ->get()
            ->all();
    }
}
