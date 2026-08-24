<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Core\Models\ContentAlias;
use Liberu\Cms\Core\Models\Site;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CoreApiController
{
    public function sites(): AnonymousResourceCollection
    {
        return JsonResource::collection(Site::query()->with('channels')->latest()->paginate());
    }

    public function site(string $site): JsonResource
    {
        $record = Site::query()->with('channels')->where('key', $site)->first();
        if (! $record) {
            throw new NotFoundHttpException;
        }

        return new JsonResource($record);
    }

    public function channels(string $site): AnonymousResourceCollection
    {
        $record = Site::query()->where('key', $site)->first();
        if (! $record) {
            throw new NotFoundHttpException;
        }

        return JsonResource::collection($record->channels()->latest()->paginate());
    }

    public function alias(string $site, string $alias): JsonResource
    {
        $record = ContentAlias::query()
            ->whereRelation('site', 'key', $site)
            ->where('alias', '/'.ltrim($alias, '/'))
            ->first();
        if (! $record) {
            throw new NotFoundHttpException;
        }

        return new JsonResource($record);
    }
}
