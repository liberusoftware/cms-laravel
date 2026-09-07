<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegration\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Liberu\Cms\ForumsIntegration\Models\ForumReference;

final class ForumReferenceQuery
{
    /** @return LengthAwarePaginator<int, ForumReference> */
    public function paginate(int $perPage = 15, string $provider = ''): LengthAwarePaginator
    {
        return ForumReference::query()->when(trim($provider) !== '', fn ($query) => $query->where('provider', trim($provider)))->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $publicId, ?int $teamId = null): ?ForumReference
    {
        return ForumReference::query()->where(['public_id' => $publicId, 'team_id' => $teamId])->first();
    }
}
