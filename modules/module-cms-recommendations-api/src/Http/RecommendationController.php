<?php

declare(strict_types=1);

namespace Liberu\Cms\RecommendationsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Recommendations\Models\RecommendationList;
use Liberu\Cms\Recommendations\Services\RecommendationService;

final class RecommendationController
{
    public function createList(Request $request, RecommendationService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'string'], 'name' => ['required', 'string'], 'kind' => ['sometimes', 'in:latest,popular,trending,editorial'], 'audience_rules' => ['array'], 'exclusions' => ['array']]);

        return response()->json(['data' => $service->createList($data['key'], $data['name'], $data['kind'] ?? 'latest', $data['audience_rules'] ?? [], $data['exclusions'] ?? [], $request->user()?->current_team_id)], 201);
    }

    public function index(Request $request, string $key, RecommendationService $service): JsonResponse
    {
        $data = $request->validate(['context' => ['sometimes', 'array'], 'exclude' => ['sometimes', 'nullable', 'string', 'max:255'], 'limit' => ['sometimes', 'integer', 'min:1', 'max:100']]);

        return response()->json(['data' => $service->recommend($key, $data['context'] ?? [], $data['exclude'] ?? null, (int) ($data['limit'] ?? 10))]);
    }

    public function update(Request $request, string $key, RecommendationService $service): JsonResponse
    {
        $list = RecommendationList::query()->where('key', $key)->where('active', true)->firstOrFail();
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:255'], 'kind' => ['sometimes', 'in:latest,popular,trending,editorial'], 'ranker' => ['sometimes', 'nullable', 'string', 'max:255'], 'audience_rules' => ['sometimes', 'array'], 'exclusions' => ['sometimes', 'array'], 'active' => ['sometimes', 'boolean']]);

        return response()->json(['data' => $service->updateList($list, $data)]);
    }

    public function remove(string $key, RecommendationService $service): JsonResponse
    {
        $list = RecommendationList::query()->where('key', $key)->where('active', true)->firstOrFail();
        $service->removeList($list);

        return response()->json([], 204);
    }

    public function addItem(Request $request, string $key, RecommendationService $service): JsonResponse
    {
        $list = RecommendationList::query()->where('key', $key)->where('active', true)->firstOrFail();
        $data = $request->validate(['item_type' => ['required', 'string'], 'item_key' => ['required', 'string'], 'title' => ['required', 'string'], 'summary' => ['nullable', 'string'], 'context' => ['array'], 'popularity_score' => ['numeric'], 'editorial_score' => ['numeric'], 'published_at' => ['nullable', 'date'], 'position' => ['integer', 'min:0']]);

        return response()->json(['data' => $service->addItem($list, $data)], 201);
    }

    public function exclude(Request $request, string $key, RecommendationService $service): JsonResponse
    {
        $list = RecommendationList::query()->where('key', $key)->where('active', true)->firstOrFail();
        $itemKey = $request->validate(['item_key' => ['required', 'string']])['item_key'];

        return response()->json(['data' => $service->exclude($list, $itemKey)]);
    }
}
