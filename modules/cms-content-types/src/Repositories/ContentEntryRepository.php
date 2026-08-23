<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\Contracts\Content\WorkflowState;

final class ContentEntryRepository implements ContentEntryRepositoryInterface
{
    public function find(int $id): ?ContentEntry
    {
        return ContentEntry::query()->find($id);
    }

    public function findBySlug(string $slug): ?ContentEntry
    {
        return ContentEntry::query()->where('slug', $slug)->first();
    }

    public function ofType(string $typeKey): array
    {
        return ContentEntry::query()
            ->whereHas('type', fn (Builder $query) => $query->where('key', $typeKey))
            ->get()
            ->all();
    }

    public function published(): array
    {
        return ContentEntry::query()
            ->where('status', WorkflowState::Published->value)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->get()
            ->all();
    }

    public function search(string $query, int $limit): array
    {
        $like = '%'.addcslashes($query, '%_\\').'%';

        return ContentEntry::query()
            ->where('status', WorkflowState::Published->value)
            ->where(function (Builder $inner): void {
                $inner->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->where(function (Builder $inner) use ($like): void {
                $inner->where('title', 'like', $like)
                    ->orWhere('data', 'like', $like);
            })
            ->limit($limit)
            ->get()
            ->all();
    }
}
