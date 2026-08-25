<?php

declare(strict_types=1);

namespace Liberu\Cms\SeoApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Seo\SeoMetadataService;

final class SeoController
{
    public function save(Request $request, string $type, int $id, SeoMetadataService $service): JsonResponse
    {
        $data = $request->validate(['title' => ['nullable', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:5000'], 'canonical_url' => ['nullable', 'url'], 'robots' => ['nullable', 'string', 'max:255'], 'structured_data' => ['array'], 'social_cards' => ['array'], 'hreflang' => ['array'], 'noindex' => ['boolean'], 'noarchive' => ['boolean']]);

        return response()->json(['data' => $service->save($type, $id, $data, $request->user()?->current_team_id)], 201);
    }

    public function show(Request $request, string $type, int $id, SeoMetadataService $service): JsonResponse
    {
        return response()->json(['data' => $service->check($type, $id, $request->user()?->current_team_id)]);
    }
}
