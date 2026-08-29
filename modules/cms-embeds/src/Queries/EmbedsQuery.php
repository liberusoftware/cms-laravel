<?php

namespace Liberu\Cms\Embeds\Queries;

use Liberu\Cms\Embeds\Models\Embed;

class EmbedsQuery
{
    public function list(int $perPage = 15, string $search = ''): mixed
    {
        return Embed::query()->with('provider')->where('status', 'published')->when($search, fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('external_key', 'like', "%{$search}%")))->paginate($perPage);
    }

    public function find(int $id): ?Embed
    {
        return Embed::with('provider')->find($id);
    }
}
