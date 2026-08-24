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

        return response()->json(['data' => $service->related($type, $id, (int) ($data['limit'] ?? 10), $data['taxonomy'] ?? [])]);
    }
}
