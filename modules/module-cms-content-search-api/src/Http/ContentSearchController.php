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
        return response()->json(['data' => $service->search($request->validate(['q' => ['required', 'string']])['q'], $request->user()?->current_team_id)]);
    }

    public function autocomplete(Request $request, ContentSearchService $service): JsonResponse
    {
        return response()->json(['data' => $service->autocomplete($request->validate(['q' => ['required', 'string']])['q'], $request->user()?->current_team_id)]);
    }

    public function analytics(Request $request, ContentSearchService $service): JsonResponse
    {
        return response()->json(['data' => $service->analytics($request->user()?->current_team_id, $request->integer('page_size', 25))]);
    }
}
