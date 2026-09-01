<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApi\Services;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\HeadlessApi\Models\PersistedQuery;

final class PersistedQueryService
{
    public function store(string $query, ?int $teamId = null): PersistedQuery
    {
        if (trim($query) === '' || strlen($query) > 100000) {
            throw ValidationException::withMessages(['query' => 'The persisted query is invalid.']);
        }
        $hash = hash('sha256', $query);

        return PersistedQuery::query()->updateOrCreate(['team_id' => $teamId, 'query_hash' => $hash], ['query_body' => $query]);
    }

    public function resolve(string $hash, ?int $teamId = null): ?PersistedQuery
    {
        if (! preg_match('/^[a-f0-9]{64}$/', $hash)) {
            throw ValidationException::withMessages(['hash' => 'The persisted query hash is invalid.']);
        }
        $query = PersistedQuery::query()->where(['team_id' => $teamId, 'query_hash' => $hash])->first();
        $query?->update(['last_used_at' => now()]);

        return $query;
    }
}
