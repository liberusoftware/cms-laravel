<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\Collections\Http\Resources\CollectionItemResource;
use Liberu\Cms\Collections\Http\Resources\CollectionResource;
use Liberu\Cms\Collections\Queries\CollectionQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class CollectionApiController
{
    public function __construct(private CollectionQuery $collections) {}

    public function index(): AnonymousResourceCollection
    {
        return CollectionResource::collection($this->collections->paginate());
    }

    public function show(string $slug): CollectionResource
    {
        $collection = $this->collections->publishedCollection($slug);
        if (! $collection) {
            throw new NotFoundHttpException;
        }

        return new CollectionResource($collection);
    }

    public function items(string $collection): AnonymousResourceCollection
    {
        try {
            return CollectionItemResource::collection($this->collections->published($collection));
        } catch (ModelNotFoundException) {
            throw new NotFoundHttpException;
        }
    }

    public function item(string $collection, string $slug): CollectionItemResource
    {
        $item = $this->collections->item($collection, $slug);
        if (! $item) {
            throw new NotFoundHttpException;
        }

        return new CollectionItemResource($item);
    }
}
