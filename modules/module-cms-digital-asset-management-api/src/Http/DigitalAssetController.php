<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagementApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\DigitalAssetManagement\Models\DigitalAsset;
use Liberu\Cms\DigitalAssetManagement\Services\DigitalAssetManagementService;

final class DigitalAssetController
{
    public function index(Request $request, DigitalAssetManagementService $service): JsonResponse
    {
        $status = $request->input('status');

        return response()->json(['data' => $service->assets($request->user()?->current_team_id, is_string($status) ? $status : null, $request->integer('page_size', 25))]);
    }

    public function store(Request $request, DigitalAssetManagementService $service): JsonResponse
    {
        return response()->json(['data' => $service->register($this->normalized($request->validate(['name' => ['required', 'string'], 'asset_type' => ['required', 'string'], 'storage_key' => ['required', 'string'], 'license' => ['nullable', 'string'], 'attribution' => ['nullable', 'string'], 'release_reference' => ['nullable', 'string'], 'expires_at' => ['nullable', 'date'], 'brand_asset' => ['boolean']])), $request->user()?->current_team_id)], 201);
    }

    public function approve(Request $request, DigitalAsset $asset, DigitalAssetManagementService $service): JsonResponse
    {
        abort_unless($asset->team_id === $request->user()?->current_team_id, 404);

        return response()->json(['data' => $service->approve($asset)]);
    }

    public function rendition(Request $request, DigitalAsset $asset, DigitalAssetManagementService $service): JsonResponse
    {
        abort_unless($asset->team_id === $request->user()?->current_team_id, 404);
        $data = $this->normalized($request->validate(['name' => ['required', 'string'], 'storage_key' => ['required', 'string']]));

        return response()->json(['data' => $service->addRendition($asset, is_string($data['name'] ?? null) ? $data['name'] : '', is_string($data['storage_key'] ?? null) ? $data['storage_key'] : '')], 201);
    }

    public function distribute(Request $request, DigitalAsset $asset, DigitalAssetManagementService $service): JsonResponse
    {
        abort_unless($asset->team_id === $request->user()?->current_team_id, 404);
        $data = $this->normalized($request->validate(['channels' => ['required', 'array']]));

        return response()->json(['data' => $service->distribute($asset, $this->normalized($data['channels'] ?? []))]);
    }

    /** @return array<string, mixed> */
    private function normalized(mixed $value): array
    {
        $data = [];
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if (is_string($key)) {
                    $data[$key] = $item;
                }
            }
        }

        return $data;
    }
}
