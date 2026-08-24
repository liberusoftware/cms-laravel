<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Core\Database\Concerns\FiltersContentQueries;
use Liberu\Cms\Pages\Contracts\PageRepositoryInterface;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Pages\Models\PageAlias;
use Liberu\Cms\Pages\Models\PageRedirect;

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

    public function findByPath(string $path): ?Page
    {
        $path = trim($path, '/');
        $page = $this->filterContentQuery('pages.find_by_path', Page::query()->where('slug', basename($path)))->get()
            ->first(fn (Page $candidate): bool => trim($candidate->path(), '/') === $path);

        if ($page instanceof Page) {
            return $page;
        }

        return $this->filterContentQuery('pages.find_by_alias', Page::query()
            ->whereKey(PageAlias::query()->where('path', '/'.$path)->value('page_id')))->first();
    }

    public function redirectForPath(string $path): ?PageRedirect
    {
        return PageRedirect::query()->where('from_path', '/'.trim($path, '/'))->where('active', true)->first();
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
