<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistant\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Liberu\Cms\MediaAssistant\Models\MediaSuggestion;

final class MediaSuggestionQuery
{
    /** @return LengthAwarePaginator<int, MediaSuggestion> */
    public function paginate(int $perPage = 15, string $assetKey = '', string $status = ''): LengthAwarePaginator
    {
        return MediaSuggestion::query()
            ->when(trim($assetKey) !== '', fn ($query) => $query->where('asset_key', trim($assetKey)))
            ->when(in_array($status, ['pending', 'accepted', 'rejected'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $publicId, ?int $teamId = null): ?MediaSuggestion
    {
        return MediaSuggestion::query()->where(['public_id' => $publicId, 'team_id' => $teamId])->first();
    }
}
