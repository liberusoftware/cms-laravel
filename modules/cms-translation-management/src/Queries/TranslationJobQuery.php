<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\TranslationManagement\Models\TranslationJob;

final class TranslationJobQuery
{
    public function paginate(int $perPage = 15, string $search = '', ?string $status = null): LengthAwarePaginator
    {
        $term = trim($search);

        return TranslationJob::query()->withCount('sourceChanges')
            ->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$term}%")->orWhere('public_id', $term)))
            ->when($status !== null && $status !== '', fn ($query) => $query->where('status', $status))
            ->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $publicId): ?TranslationJob
    {
        return TranslationJob::query()->with('sourceChanges')->where('public_id', $publicId)->first();
    }
}
