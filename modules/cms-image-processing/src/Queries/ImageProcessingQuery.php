<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessing\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Liberu\Cms\ImageProcessing\Models\ProcessingProfile;

final class ImageProcessingQuery
{
    /** @return LengthAwarePaginator<int, ProcessingProfile> */
    public function profiles(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return ProcessingProfile::query()->when(trim($search) !== '', fn ($query) => $query->where('key', 'like', '%'.trim($search).'%'))->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function profile(string $key, ?int $teamId = null): ?ProcessingProfile
    {
        return ProcessingProfile::query()->where(['team_id' => $teamId])->where(fn ($query) => $query->where('public_id', $key)->orWhere('key', $key))->first();
    }
}
