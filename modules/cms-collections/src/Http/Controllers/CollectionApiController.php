<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\Collections\Http\Resources\CollectionItemResource;
use Liberu\Cms\Collections\Http\Resources\CollectionResource;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\Collections\Models\CollectionItem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CollectionApiController
{
    public function index(): AnonymousResourceCollection
    {
        return CollectionResource::collection(Collection::query()->with(['items' => fn ($query) => $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())])->latest()->paginate());
    }

    public function show(string $slug): CollectionResource
    {
        $collection = Collection::query()->where('slug', $slug)->with(['items' => fn ($query) => $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())])->first();
        if (! $collection) {
            throw new NotFoundHttpException;
        }

        return new CollectionResource($collection);
    }

    public function items(string $collection): AnonymousResourceCollection
    {
        $owner = Collection::query()->where('slug', $collection)->first();
        if (! $owner) {
            throw new NotFoundHttpException;
        }

        return CollectionItemResource::collection($owner->items()->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())->latest()->paginate());
    }

    public function item(string $collection, string $slug): CollectionItemResource
    {
        $item = CollectionItem::query()->whereHas('collection', fn ($query) => $query->where('slug', $collection))->where('slug', $slug)->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())->with('collection')->first();
        if (! $item) {
            throw new NotFoundHttpException;
        }

        return new CollectionItemResource($item);
    }
}
