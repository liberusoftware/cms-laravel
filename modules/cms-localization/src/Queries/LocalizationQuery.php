<?php

declare(strict_types=1);

namespace Liberu\Cms\Localization\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Liberu\Cms\Localization\Models\Locale;
use Liberu\Cms\Localization\Models\LocaleVariant;

final class LocalizationQuery
{
    /** @return LengthAwarePaginator<int, Locale> */
    public function locales(int $perPage = 15, ?int $teamId = null, string $search = ''): LengthAwarePaginator
    {
        return Locale::query()->where('team_id', $teamId)->when(trim($search) !== '', fn ($query) => $query->where('locale', 'like', '%'.trim($search).'%'))->latest()->paginate(max(1, min($perPage, 100)));
    }

    /** @return LengthAwarePaginator<int, LocaleVariant> */
    public function variants(int $perPage = 15, ?int $teamId = null, string $search = ''): LengthAwarePaginator
    {
        return LocaleVariant::query()->where('team_id', $teamId)->when(trim($search) !== '', fn ($query) => $query->where(function ($query) use ($search): void {
            $query->where('source_key', 'like', '%'.trim($search).'%')->orWhere('locale', 'like', '%'.trim($search).'%');
        }))->latest()->paginate(max(1, min($perPage, 100)));
    }
}
