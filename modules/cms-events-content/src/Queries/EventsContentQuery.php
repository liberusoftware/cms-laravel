<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContent\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\EventsContent\Models\Event;

final class EventsContentQuery
{
    public function calendar(int $perPage = 15, string $search = '', bool $includeArchived = false): LengthAwarePaginator
    {
        $term = trim($search);

        return Event::query()->with(['venue', 'sessions.speakers'])->when(! $includeArchived, fn ($query) => $query->where('status', 'published')->whereNull('archived_at'))->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('title', 'like', "%{$term}%")->orWhere('key', 'like', "%{$term}%")))->orderBy('starts_at')->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $key): ?Event
    {
        return Event::query()->where('key', $key)->with(['venue', 'sessions.speakers', 'registrations'])->first();
    }
}
