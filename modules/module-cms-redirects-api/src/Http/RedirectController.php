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

        return response()->json(['data' => $service->resolve($data['path'], (int) $request->integer('max_hops', 10))]);
    }

    public function store(Request $request, RedirectService $service): JsonResponse
    {
        $data = $request->validate(['from_path' => ['required', 'string', 'max:2048'], 'to_path' => ['required', 'string', 'max:2048'], 'status_code' => ['sometimes', 'integer', 'in:301,302,307,308'], 'source' => ['sometimes', 'nullable', 'string', 'max:255']]);
        $redirect = $service->create($data['from_path'], $data['to_path'], (int) ($data['status_code'] ?? 301), $data['source'] ?? 'api', $request->user()?->current_team_id);

        return response()->json(['data' => $redirect], 201);
    }

    public function import(Request $request, RedirectService $service): JsonResponse
    {
        $rows = $request->validate(['rows' => ['required', 'array'], 'rows.*.from_path' => ['required', 'string'], 'rows.*.to_path' => ['required', 'string'], 'rows.*.status_code' => ['sometimes', 'integer', 'in:301,302,307,308']])['rows'];

        return response()->json(['data' => ['imported' => $service->import($rows, $request->user()?->current_team_id)]], 201);
    }

    public function slugChange(Request $request, RedirectService $service): JsonResponse
    {
        $data = $request->validate(['old_path' => ['required', 'string'], 'new_path' => ['required', 'string']]);

        return response()->json(['data' => $service->recordSlugChange($data['old_path'], $data['new_path'], $request->user()?->current_team_id)], 201);
    }

    public function suggestions(Request $request, RedirectService $service): JsonResponse
    {
        $data = $request->validate(['path' => ['required', 'string'], 'limit' => ['sometimes', 'integer', 'min:1', 'max:20']]);

        return response()->json(['data' => $service->suggestions($data['path'], (int) ($data['limit'] ?? 5))]);
    }
}
