<?php

declare(strict_types=1);

namespace Liberu\Cms\PublishingApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Publishing\Models\PublicationRelease;
use Liberu\Cms\Publishing\Services\PublishingService;

final class ReleaseController
{
    public function store(Request $request, PublishingService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'string'], 'publish_at' => ['nullable', 'date'], 'embargo_until' => ['nullable', 'date'], 'expires_at' => ['nullable', 'date'], 'review_at' => ['nullable', 'date'], 'recurrence' => ['nullable', 'string'], 'targets' => ['array'], 'cache_tags' => ['array']]);

        return response()->json(['data' => $service->create([...$data, 'team_id' => $request->user()?->current_team_id])], 201);
    }

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

    public function schedule(string $key, PublishingService $service): JsonResponse
    {
        $release = $service->schedule(PublicationRelease::query()->where('key', $key)->firstOrFail());

        return response()->json(['data' => ['key' => $release->key, 'state' => $release->state]]);
    }

    public function archive(string $key, PublishingService $service): JsonResponse
    {
        $release = $service->archive(PublicationRelease::query()->where('key', $key)->firstOrFail());

        return response()->json(['data' => ['key' => $release->key, 'state' => $release->state]]);
    }

    public function processDue(PublishingService $service): JsonResponse
    {
        return response()->json(['data' => $service->processDue()]);
    }
}
