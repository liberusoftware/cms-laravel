<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsApi\Http;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\Cms\StructuredCollections\Actions\StructuredCollectionMutationService;
use Liberu\Cms\StructuredCollections\Queries\StructuredCollectionQuery;
use Liberu\Cms\StructuredCollectionsApi\Http\Resources\StructuredCollectionRecordResource;
use Liberu\Cms\StructuredCollectionsApi\Http\Resources\StructuredCollectionResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class StructuredCollectionsController
{
    public function index(Request $request, StructuredCollectionQuery $query): mixed
    {
        $data = $request->validate(['search' => ['sometimes', 'nullable', 'string', 'max:255'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);

        return StructuredCollectionResource::collection($query->paginate((int) ($data['per_page'] ?? 15), $data['search'] ?? ''));
    }

    public function show(string $slug, StructuredCollectionQuery $query): StructuredCollectionResource
    {
        $collection = $query->collection($slug);
        if (! $collection) {
            throw new NotFoundHttpException;
        }

        return new StructuredCollectionResource($collection);
    }

    public function create(Request $request, StructuredCollectionMutationService $service): StructuredCollectionResource
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'slug' => ['nullable', 'string', 'max:255'], 'type' => ['required', 'string', 'max:64'], 'description' => ['nullable', 'string'], 'schema' => ['nullable', 'array']]);

        return new StructuredCollectionResource($service->create($data));
    }

    public function update(Request $request, string $slug, StructuredCollectionQuery $query, StructuredCollectionMutationService $service): StructuredCollectionResource
    {
        $collection = $query->collection($slug);
        if (! $collection) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:255'], 'slug' => ['sometimes', 'string', 'max:255'], 'type' => ['sometimes', 'string', 'max:64'], 'description' => ['nullable', 'string'], 'schema' => ['nullable', 'array']]);

        return new StructuredCollectionResource($service->update($collection, $data));
    }

    public function delete(string $slug, StructuredCollectionQuery $query, StructuredCollectionMutationService $service): Response
    {
        $collection = $query->collection($slug);
        if (! $collection) {
            throw new NotFoundHttpException;
        }
        $service->delete($collection);

        return response()->noContent();
    }

    public function records(Request $request, string $slug, StructuredCollectionQuery $query): mixed
    {
        if (! $query->collection($slug)) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['search' => ['sometimes', 'nullable', 'string', 'max:255'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);

        return StructuredCollectionRecordResource::collection($query->records($slug, (int) ($data['per_page'] ?? 15), $data['search'] ?? ''));
    }

    public function record(string $slug, string $record, StructuredCollectionQuery $query): StructuredCollectionRecordResource
    {
        $model = $query->record($slug, $record);
        if (! $model) {
            throw new NotFoundHttpException;
        }

        return new StructuredCollectionRecordResource($model);
    }

    public function createRecord(Request $request, string $slug, StructuredCollectionQuery $query, StructuredCollectionMutationService $service): StructuredCollectionRecordResource
    {
        $collection = $query->collection($slug);
        if (! $collection) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'slug' => ['nullable', 'string', 'max:255'], 'content' => ['nullable', 'string'], 'excerpt' => ['nullable', 'string'], 'data' => ['nullable', 'array'], 'metadata' => ['nullable', 'array'], 'status' => ['nullable', 'string', 'max:32'], 'published_at' => ['nullable', 'date']]);

        return new StructuredCollectionRecordResource($service->createRecord($collection, $data));
    }
}
