<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentSearchApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Search\Services\ContentSearchService;

final class ContentSearchController
{
    public function search(Request $request, ContentSearchService $service): JsonResponse
    {
        $data = $this->normalized($request->validate(['q' => ['required', 'string']]));

        return response()->json(['data' => $service->search(is_string($data['q'] ?? null) ? $data['q'] : '', $request->user()?->current_team_id)]);
    }

    public function autocomplete(Request $request, ContentSearchService $service): JsonResponse
    {
        $data = $this->normalized($request->validate(['q' => ['required', 'string']]));

        return response()->json(['data' => $service->autocomplete(is_string($data['q'] ?? null) ? $data['q'] : '', $request->user()?->current_team_id)]);
    }

    public function analytics(Request $request, ContentSearchService $service): JsonResponse
    {
        return response()->json(['data' => $service->analytics($request->user()?->current_team_id, $request->integer('page_size', 25))]);
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
