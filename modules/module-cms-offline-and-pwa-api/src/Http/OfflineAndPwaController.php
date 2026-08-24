<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwaApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\OfflineAndPwa\Models\PwaConfiguration;
use Liberu\Cms\OfflineAndPwa\Services\OfflineAndPwaService;

final class OfflineAndPwaController
{
    public function show(Request $request, OfflineAndPwaService $service): JsonResponse
    {
        $data = $request->validate(['site_key' => ['required', 'string', 'max:100'], 'asset' => ['sometimes', 'string', 'in:manifest,service-worker']]);
        $configuration = PwaConfiguration::query()->where('site_key', $data['site_key'])->firstOrFail();
        $asset = $data['asset'] ?? 'manifest';
        if ($asset === 'service-worker') {
            return response($service->serviceWorker($configuration), 200, ['Content-Type' => 'application/javascript; charset=UTF-8', 'Cache-Control' => 'no-cache']);
        }

        return response()->json(['data' => $service->manifest($configuration), 'service_worker_version' => $configuration->service_worker_version]);
    }
}
