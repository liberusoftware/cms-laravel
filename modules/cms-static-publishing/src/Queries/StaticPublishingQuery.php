<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishing\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\StaticPublishing\Models\StaticBuild;

final readonly class StaticPublishingQuery
{
    public function builds(int $perPage = 15): LengthAwarePaginator
    {
        return StaticBuild::query()->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function build(int|string $id): ?StaticBuild
    {
        return StaticBuild::query()->whereKey($id)->first();
    }
}
