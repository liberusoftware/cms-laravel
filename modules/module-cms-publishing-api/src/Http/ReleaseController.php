<?php

declare(strict_types=1);

namespace Liberu\Cms\PublishingApi\Http;

use Illuminate\Http\JsonResponse;
use Liberu\Cms\Publishing\Models\PublicationRelease;
use Liberu\Cms\Publishing\Services\PublishingService;

final class ReleaseController
{
    public function show(string $key): JsonResponse
    {
        $release = PublicationRelease::query()->where('key', $key)->firstOrFail();

        return response()->json(['data' => ['key' => $release->key, 'state' => $release->state, 'publish_at' => $release->publish_at?->toISOString(), 'embargo_until' => $release->embargo_until?->toISOString(), 'expires_at' => $release->expires_at?->toISOString(), 'review_at' => $release->review_at?->toISOString(), 'targets' => $release->targets, 'cache_tags' => $release->cache_tags]]);
    }

    public function publish(string $key, PublishingService $service): JsonResponse
    {
        $release = $service->publish(PublicationRelease::query()->where('key', $key)->firstOrFail());

        return response()->json(['data' => ['key' => $release->key, 'state' => $release->state]]);
    }

    public function unpublish(string $key, PublishingService $service): JsonResponse
    {
        $release = $service->unpublish(PublicationRelease::query()->where('key', $key)->firstOrFail());

        return response()->json(['data' => ['key' => $release->key, 'state' => $release->state]]);
    }
}
