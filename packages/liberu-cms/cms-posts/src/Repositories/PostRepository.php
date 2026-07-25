<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Posts\Contracts\PostRepositoryInterface;
use Liberu\Cms\Posts\Models\Post;

final class PostRepository implements PostRepositoryInterface
{
    public function find(int $id): ?Post
    {
        return Post::query()->find($id);
    }

    public function findBySlug(string $slug): ?Post
    {
        return Post::query()->where('slug', $slug)->first();
    }

    public function published(): array
    {
        return $this->live()->get()->all();
    }

    public function featured(): array
    {
        return $this->live()->where('is_featured', true)->get()->all();
    }

    public function byCategory(string $categorySlug): array
    {
        return $this->live()
            ->whereHas('categories', fn (Builder $query) => $query->where('slug', $categorySlug))
            ->get()
            ->all();
    }

    public function byTag(string $tagSlug): array
    {
        return $this->live()
            ->whereHas('tags', fn (Builder $query) => $query->where('slug', $tagSlug))
            ->get()
            ->all();
    }

    public function search(string $query, int $limit): array
    {
        $like = '%'.addcslashes($query, '%_\\').'%';

        return $this->live()
            ->where(function (Builder $inner) use ($like): void {
                $inner->where('title', 'like', $like)
                    ->orWhere('content', 'like', $like)
                    ->orWhere('excerpt', 'like', $like);
            })
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * @return Builder<Post>
     */
    private function live(): Builder
    {
        return Post::query()
            ->where('status', WorkflowState::Published->value)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at');
    }
}
