<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistant\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Liberu\Cms\ExperienceAssistant\Models\ExperienceSuggestion;

final class ExperienceSuggestionQuery
{
    /** @return LengthAwarePaginator<int, ExperienceSuggestion> */
    public function paginate(int $perPage = 15, string $surface = '', string $status = ''): LengthAwarePaginator
    {
        return ExperienceSuggestion::query()->when(trim($surface) !== '', fn ($query) => $query->where('surface', 'like', '%'.trim($surface).'%'))->when(in_array($status, ['pending', 'approved'], true), fn ($query) => $query->where('status', $status))->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $publicId, ?int $teamId = null): ?ExperienceSuggestion
    {
        return ExperienceSuggestion::query()->where(['public_id' => $publicId, 'team_id' => $teamId])->first();
    }
}
