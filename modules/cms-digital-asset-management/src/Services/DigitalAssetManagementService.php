<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\DigitalAssetManagement\Models\DigitalAsset;

final readonly class DigitalAssetManagementService
{
    /** @return LengthAwarePaginator<int, DigitalAsset> */
    public function assets(?int $teamId, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        $configuredMax = config('digital-asset-management.pagination.max', 100);
        $maxPerPage = is_int($configuredMax) ? $configuredMax : 100;

        return DigitalAsset::query()->where('team_id', $teamId)->when($status !== null, fn ($q) => $q->where('status', $status))->latest()->paginate(max(1, min($perPage, $maxPerPage)));
    }

    /** @param array<string, mixed> $data */
    public function register(array $data, ?int $teamId = null): DigitalAsset
    {
        if (blank($data['name'] ?? null) || blank($data['asset_type'] ?? null) || blank($data['storage_key'] ?? null)) {
            throw ValidationException::withMessages(['asset' => 'Name, type, and storage key are required.']);
        }
        $expiresAt = $data['expires_at'] ?? null;
        if (is_string($expiresAt) && now()->isAfter($expiresAt)) {
            throw ValidationException::withMessages(['expires_at' => 'The asset expiry must be in the future.']);
        }

        return DigitalAsset::query()->create([...$data, 'team_id' => $teamId]);
    }

    public function approve(DigitalAsset $asset): DigitalAsset
    {
        $asset->update(['approved' => true, 'approved_at' => now(), 'status' => 'approved']);

        return $asset->fresh() ?? $asset;
    }

    public function addRendition(DigitalAsset $asset, string $name, string $storageKey): DigitalAsset
    {
        if ($name === '' || $storageKey === '') {
            throw ValidationException::withMessages(['rendition' => 'A rendition name and storage key are required.']);
        }
        $renditions = is_array($asset->renditions) ? $asset->renditions : [];
        $asset->update(['renditions' => [...$renditions, $name => $storageKey]]);

        return $asset->fresh() ?? $asset;
    }

    /** @param array<string, mixed> $channels */
    public function distribute(DigitalAsset $asset, array $channels): DigitalAsset
    {
        if ($channels === []) {
            throw ValidationException::withMessages(['distribution' => 'At least one distribution channel is required.']);
        }
        $asset->update(['distribution' => $channels, 'status' => 'distributed']);

        return $asset->fresh() ?? $asset;
    }
}
