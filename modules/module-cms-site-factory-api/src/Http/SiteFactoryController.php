<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactoryApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\SiteFactory\Services\SiteFactoryService;

final class SiteFactoryController
{
    public function store(Request $request, SiteFactoryService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'alpha_dash'], 'name' => ['required', 'string', 'max:255'], 'template' => ['sometimes', 'nullable', 'string'], 'domain' => ['sometimes', 'nullable', 'string']]);

        return response()->json(['data' => $service->provision($data['key'], $data['name'], $data['template'] ?? null, $data['domain'] ?? null)], 201);
    }
}
