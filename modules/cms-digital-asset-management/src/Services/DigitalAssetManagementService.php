<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagement\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\DigitalAssetManagement\Models\DigitalAsset;

final readonly class DigitalAssetManagementService
{
    public function assets(?int $teamId, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return DigitalAsset::query()->where('team_id', $teamId)->when($status !== null, fn ($q) => $q->where('status', $status))->latest()->paginate(max(1, min($perPage, (int) config('digital-asset-management.pagination.max', 100))));
    }

    public function register(array $data, ?int $teamId = null): DigitalAsset
    {
        if (blank($data['name'] ?? null) || blank($data['asset_type'] ?? null) || blank($data['storage_key'] ?? null)) {
            throw ValidationException::withMessages(['asset' => 'Name, type, and storage key are required.']);
        }
        if (($data['expires_at'] ?? null) !== null && now()->isAfter($data['expires_at'])) {
            throw ValidationException::withMessages(['expires_at' => 'The asset expiry must be in the future.']);
        }

        return DigitalAsset::query()->create([...$data, 'team_id' => $teamId]);
    }

    public function approve(DigitalAsset $asset): DigitalAsset
    {
        $asset->update(['approved' => true, 'approved_at' => now(), 'status' => 'approved']);

        return $asset->fresh();
    }

    public function addRendition(DigitalAsset $asset, string $name, string $storageKey): DigitalAsset
    {
        if ($name === '' || $storageKey === '') {
            throw ValidationException::withMessages(['rendition' => 'A rendition name and storage key are required.']);
        }
        $asset->update(['renditions' => [...($asset->renditions ?? []), $name => $storageKey]]);

        return $asset->fresh();
    }

    public function distribute(DigitalAsset $asset, array $channels): DigitalAsset
    {
        if ($channels === []) {
            throw ValidationException::withMessages(['distribution' => 'At least one distribution channel is required.']);
        }
        $asset->update(['distribution' => $channels, 'status' => 'distributed']);

        return $asset->fresh();
    }
}
