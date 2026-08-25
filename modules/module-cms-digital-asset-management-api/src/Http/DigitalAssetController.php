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
        return response()->json(['data' => $service->assets($request->user()?->current_team_id, $request->input('status'), $request->integer('page_size', 25))]);
    }

    public function store(Request $request, DigitalAssetManagementService $service): JsonResponse
    {
        return response()->json(['data' => $service->register($request->validate(['name' => ['required', 'string'], 'asset_type' => ['required', 'string'], 'storage_key' => ['required', 'string'], 'license' => ['nullable', 'string'], 'attribution' => ['nullable', 'string'], 'release_reference' => ['nullable', 'string'], 'expires_at' => ['nullable', 'date'], 'brand_asset' => ['boolean']]), $request->user()?->current_team_id)], 201);
    }

    public function approve(DigitalAsset $asset, DigitalAssetManagementService $service): JsonResponse
    {
        return response()->json(['data' => $service->approve($asset)]);
    }

    public function rendition(Request $request, DigitalAsset $asset, DigitalAssetManagementService $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string'], 'storage_key' => ['required', 'string']]);

        return response()->json(['data' => $service->addRendition($asset, $data['name'], $data['storage_key'])], 201);
    }

    public function distribute(Request $request, DigitalAsset $asset, DigitalAssetManagementService $service): JsonResponse
    {
        return response()->json(['data' => $service->distribute($asset, $request->validate(['channels' => ['required', 'array']])['channels'])]);
    }
}
