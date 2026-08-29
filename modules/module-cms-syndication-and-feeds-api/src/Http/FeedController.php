<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeedsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\SyndicationAndFeeds\Models\Feed;
use Liberu\Cms\SyndicationAndFeeds\Services\FeedService;

final class FeedController
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Feed::query()->where('active', true)->withCount('items')->latest()->get()]);
    }

    public function create(Request $request, FeedService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:255'], 'title' => ['required', 'string', 'max:255'], 'format' => ['sometimes', 'in:rss,atom,json'], 'source_url' => ['nullable', 'url'], 'mapping' => ['array']]);

        return response()->json(['data' => $service->create($data['key'], $data['title'], $data['format'] ?? 'rss', $data['source_url'] ?? null, $data['mapping'] ?? [])], 201);
    }

    public function update(Request $request, string $feed, FeedService $service): JsonResponse
    {
        $model = Feed::query()->where('key', $feed)->where('active', true)->firstOrFail();
        $data = $request->validate(['title' => ['sometimes', 'string', 'max:255'], 'format' => ['sometimes', 'in:rss,atom,json'], 'source_url' => ['nullable', 'url'], 'mapping' => ['sometimes', 'array'], 'active' => ['sometimes', 'boolean']]);

        return response()->json(['data' => $service->update($model, $data)]);
    }

    public function delete(string $feed, FeedService $service): JsonResponse
    {
        $model = Feed::query()->where('key', $feed)->where('active', true)->firstOrFail();
        $service->remove($model);

        return response()->json([], 204);
    }

    public function show(string $feed, FeedService $service): JsonResponse
    {
        $model = Feed::query()->where('key', $feed)->where('active', true)->firstOrFail();

        return response()->json(['data' => ['feed' => $model, 'body' => $service->render($model)]]);
    }

    public function item(Request $request, string $feed, FeedService $service): JsonResponse
    {
        $model = Feed::query()->where('key', $feed)->where('active', true)->firstOrFail();
        $data = $request->validate(['external_id' => ['required', 'string'], 'title' => ['required', 'string'], 'url' => ['required', 'url'], 'summary' => ['nullable', 'string'], 'content' => ['nullable', 'string'], 'attribution' => ['array'], 'payload' => ['array']]);

        return response()->json(['data' => $service->addItem($model, $data)], 201);
    }

    public function import(Request $request, string $feed, FeedService $service): JsonResponse
    {
        $model = Feed::query()->where('key', $feed)->where('active', true)->firstOrFail();
        $data = $request->validate(['xml' => ['required', 'string']]);

        return response()->json(['data' => ['imported' => $service->import($model, $data['xml'])]]);
    }

    public function syndicate(Request $request, string $feed, FeedService $service): JsonResponse
    {
        $model = Feed::query()->where('key', $feed)->where('active', true)->firstOrFail();
        $destination = $request->validate(['destination' => ['required', 'url']])['destination'];

        return response()->json(['data' => $service->syndicate($model, $destination)], 202);
    }
}
