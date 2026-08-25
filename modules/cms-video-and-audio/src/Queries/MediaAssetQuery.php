<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\VideoAndAudio\Models\MediaAsset;

final class MediaAssetQuery
{
    public function paginate(int $perPage = 15, string $search = '', ?string $kind = null): LengthAwarePaginator
    {
        $term = trim($search);
        return MediaAsset::query()->when($term !== '', fn ($query) => $query->where('title', 'like', "%{$term}%"))->when($kind !== null && $kind !== '', fn ($query) => $query->where('kind', $kind))->latest()->paginate(max(1, min(100, $perPage)));
    }
    public function find(string $publicId): ?MediaAsset { return MediaAsset::query()->with('tracks')->where('public_id', $publicId)->first(); }
}
