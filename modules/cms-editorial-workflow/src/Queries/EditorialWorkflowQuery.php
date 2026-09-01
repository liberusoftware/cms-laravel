<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflow\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Liberu\Cms\EditorialWorkflow\Models\EditorialWorkflow;

final class EditorialWorkflowQuery
{
    /** @return LengthAwarePaginator<int, EditorialWorkflow> */
    public function paginate(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return EditorialWorkflow::query()->when(trim($search) !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', '%'.trim($search).'%')->orWhere('key', 'like', '%'.trim($search).'%')))->with(['states', 'transitions'])->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $publicId, ?int $teamId = null): ?EditorialWorkflow
    {
        return EditorialWorkflow::query()->where(['public_id' => $publicId, 'team_id' => $teamId])->with(['states', 'transitions'])->first();
    }
}
