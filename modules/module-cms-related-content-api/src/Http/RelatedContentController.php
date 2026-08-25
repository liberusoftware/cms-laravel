<?php

declare(strict_types=1);

namespace Liberu\Cms\RelatedContentApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\RelatedContent\Services\RelatedContentService;

final class RelatedContentController
{
    public function index(Request $request, string $type, int $id, RelatedContentService $service): JsonResponse
    {
        $data = $request->validate(['limit' => ['sometimes', 'integer', 'min:1', 'max:100'], 'taxonomy' => ['sometimes', 'array']]);

        return response()->json(['data' => $service->related($type, $id, (int) ($data['limit'] ?? 10), $data['taxonomy'] ?? [], $request->user()?->current_team_id)]);
    }

    public function relate(Request $request, string $type, int $id, RelatedContentService $service): JsonResponse
    {
        $data = $request->validate(['target_type' => ['required', 'string'], 'target_id' => ['required', 'integer', 'min:1'], 'mode' => ['sometimes', 'in:manual,rule,search,similarity,recency,taxonomy'], 'score' => ['sometimes', 'numeric', 'between:0,1'], 'taxonomy' => ['array']]);

        return response()->json(['data' => $service->relate($type, $id, $data['target_type'], $data['target_id'], $data['mode'] ?? 'manual', (float) ($data['score'] ?? 1), $data['taxonomy'] ?? [], $request->user()?->current_team_id)], 201);
    }

    public function exclude(Request $request, string $type, int $id, RelatedContentService $service): JsonResponse
    {
        $data = $request->validate(['target_type' => ['required', 'string'], 'target_id' => ['required', 'integer', 'min:1']]);

        return response()->json(['data' => $service->exclude($type, $id, $data['target_type'], $data['target_id'], $request->user()?->current_team_id)]);
    }
}
