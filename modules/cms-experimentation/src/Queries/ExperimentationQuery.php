<?php

declare(strict_types=1);

namespace Liberu\Cms\Experimentation\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\Experimentation\Models\Experiment;

final class ExperimentationQuery
{
    public function list(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        $term = trim($search);

        return Experiment::query()->with('variants')->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('key', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%")))->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $key): ?Experiment
    {
        return Experiment::query()->where('key', $key)->with('variants')->first();
    }

    public function active(string $key): ?Experiment
    {
        return Experiment::query()->where('key', $key)->where('status', 'running')->with('variants')->first();
    }
}
