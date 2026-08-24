<?php

declare(strict_types=1);

namespace Liberu\Cms\RecommendationsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Recommendations\Services\RecommendationService;

final class RecommendationController
{
    public function index(Request $request, string $key, RecommendationService $service): JsonResponse
    {
        $data = $request->validate(['context' => ['sometimes', 'array'], 'exclude' => ['sometimes', 'nullable', 'string', 'max:255'], 'limit' => ['sometimes', 'integer', 'min:1', 'max:100']]);

        return response()->json(['data' => $service->recommend($key, $data['context'] ?? [], $data['exclude'] ?? null, (int) ($data['limit'] ?? 10))]);
    }
}
