<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigration\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Liberu\Cms\WordPressMigration\Models\WordPressMigration;
use Liberu\Cms\WordPressMigration\Models\WordPressMigrationRecord;

final readonly class WordPressMigrationQuery
{
    public function __construct(private TenantContextInterface $tenant) {}

    private function tenantId(?int $teamId): int|string|null
    {
        return $teamId ?? $this->tenant->tenantId();
    }

    public function migrations(int $perPage = 15, ?int $teamId = null): LengthAwarePaginator
    {
        return WordPressMigration::query()->where('team_id', $this->tenantId($teamId))->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function migration(int|string $id, ?int $teamId = null): ?WordPressMigration
    {
        return WordPressMigration::query()->whereKey($id)->where('team_id', $this->tenantId($teamId))->first();
    }

    public function records(WordPressMigration $migration, int $perPage = 25): LengthAwarePaginator
    {
        return WordPressMigrationRecord::query()->where('migration_id', $migration->id)->latest()->paginate(max(1, min(100, $perPage)));
    }
}
