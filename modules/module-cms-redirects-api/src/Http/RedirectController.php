<?php

declare(strict_types=1);

namespace Liberu\Cms\RedirectsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Redirects\Services\RedirectService;

final class RedirectController
{
    public function resolve(Request $request, RedirectService $service): JsonResponse
    {
        $data = $request->validate(['path' => ['required', 'string', 'max:2048']]);

        return response()->json(['data' => $service->resolve($data['path'])]);
    }

    public function store(Request $request, RedirectService $service): JsonResponse
    {
        $data = $request->validate(['from_path' => ['required', 'string', 'max:2048'], 'to_path' => ['required', 'string', 'max:2048'], 'status_code' => ['sometimes', 'integer', 'in:301,302,307,308'], 'source' => ['sometimes', 'nullable', 'string', 'max:255']]);
        $redirect = $service->create($data['from_path'], $data['to_path'], (int) ($data['status_code'] ?? 301), $data['source'] ?? 'api');

        return response()->json(['data' => $redirect], 201);
    }
}
