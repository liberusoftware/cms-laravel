<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperations\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Liberu\Cms\FormOperations\Models\OperationalSubmission;

final class OperationalSubmissionQuery
{
    /** @return LengthAwarePaginator<int, OperationalSubmission> */
    public function paginate(int $perPage = 15, ?int $formId = null): LengthAwarePaginator
    {
        return OperationalSubmission::query()->when($formId !== null, fn ($query) => $query->where('form_id', $formId))->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $publicId, ?int $teamId = null): ?OperationalSubmission
    {
        return OperationalSubmission::query()->where(['public_id' => $publicId, 'team_id' => $teamId])->first();
    }
}
