<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeedsApi\Http;

use Illuminate\Http\JsonResponse;
use Liberu\Cms\SyndicationAndFeeds\Models\Feed;
use Liberu\Cms\SyndicationAndFeeds\Services\FeedService;

final class FeedController
{
    public function show(string $feed, FeedService $service): JsonResponse
    {
        $model = Feed::query()->where('key', $feed)->where('active', true)->firstOrFail();

        return response()->json(['data' => ['feed' => $model, 'body' => $service->render($model)]]);
    }
}
