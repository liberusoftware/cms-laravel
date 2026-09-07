<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModesApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\DisplayModes\Services\DisplayModesService;

final class DisplayModesController
{
    public function index(Request $request, DisplayModesService $service): JsonResponse
    {
        $contentType = $request->input('content_type');

        return response()->json(['data' => $service->modes($request->user()?->current_team_id, is_string($contentType) ? $contentType : null, $request->integer('page_size', 25))]);
    }

    public function store(Request $request, DisplayModesService $service): JsonResponse
    {
        return response()->json(['data' => $service->create($this->normalized($request->validate([
            'name' => ['required', 'string'], 'slug' => ['required', 'string'], 'content_type' => ['required', 'string'],
            'mode_type' => ['nullable', 'in:view,form'], 'formatters' => ['array'], 'configuration' => ['array'],
            'responsive_variants' => ['array'], 'projection' => ['array'], 'active' => ['boolean'],
        ])), $request->user()?->current_team_id)], 201);
    }

    public function select(Request $request, DisplayModesService $service): JsonResponse
    {
        $data = $this->normalized($request->validate(['content_type' => ['required', 'string'], 'slug' => ['nullable', 'string'], 'variant' => ['nullable', 'string']]));

        return response()->json(['data' => $service->select(is_string($data['content_type'] ?? null) ? $data['content_type'] : '', $request->user()?->current_team_id, is_string($data['slug'] ?? null) ? $data['slug'] : 'default', is_string($data['variant'] ?? null) ? $data['variant'] : null)]);
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
