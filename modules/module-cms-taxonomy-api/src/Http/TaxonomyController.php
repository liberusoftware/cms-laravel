<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyApi\Http;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\Cms\Taxonomy\Queries\TaxonomyQuery;
use Liberu\Cms\Taxonomy\Services\TaxonomyService;
use Liberu\Cms\TaxonomyApi\Http\Resources\TaxonomyResource;
use Liberu\Cms\TaxonomyApi\Http\Resources\TermResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TaxonomyController
{
    public function index(Request $request, TaxonomyQuery $query): mixed
    {
        $data = $request->validate(['key' => ['sometimes', 'nullable', 'string', 'max:255'], 'search' => ['sometimes', 'nullable', 'string', 'max:255'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        return TaxonomyResource::collection($query->taxonomies($data['key'] ?? null, $data['search'] ?? null, (int) ($data['per_page'] ?? 15)));
    }

    public function show(int $taxonomy, TaxonomyQuery $query): TaxonomyResource
    {
        $model = $query->taxonomy($taxonomy);
        if (! $model) throw new NotFoundHttpException;
        return new TaxonomyResource($model);
    }

    public function create(Request $request, TaxonomyService $service): TaxonomyResource
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:255'], 'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'hierarchical' => ['sometimes', 'boolean'], 'exclusive' => ['sometimes', 'boolean']]);
        return new TaxonomyResource($service->create($data['key'], $data['name'], (bool) ($data['hierarchical'] ?? true), (bool) ($data['exclusive'] ?? false), description: $data['description'] ?? null));
    }

    public function update(Request $request, int $taxonomy, TaxonomyQuery $query, TaxonomyService $service): TaxonomyResource
    {
        $model = $query->taxonomy($taxonomy);
        if (! $model) throw new NotFoundHttpException;
        $data = $request->validate(['key' => ['sometimes', 'string', 'max:255'], 'name' => ['sometimes', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'hierarchical' => ['sometimes', 'boolean'], 'exclusive' => ['sometimes', 'boolean']]);
        return new TaxonomyResource($service->update($model, $data));
    }

    public function delete(int $taxonomy, TaxonomyQuery $query, TaxonomyService $service): Response
    {
        $model = $query->taxonomy($taxonomy);
        if (! $model) throw new NotFoundHttpException;
        $service->delete($model);
        return response()->noContent();
    }

    public function terms(Request $request, int $taxonomy, TaxonomyQuery $query): mixed
    {
        if (! $query->taxonomy($taxonomy)) throw new NotFoundHttpException;
        $data = $request->validate(['search' => ['sometimes', 'nullable', 'string', 'max:255']]);
        return TermResource::collection(collect($query->terms($taxonomy, $data['search'] ?? null)));
    }

    public function addTerm(Request $request, int $taxonomy, TaxonomyQuery $query, TaxonomyService $service): TermResource
    {
        $model = $query->taxonomy($taxonomy);
        if (! $model) throw new NotFoundHttpException;
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'slug' => ['nullable', 'string', 'max:255'], 'parent_id' => ['nullable', 'integer'], 'synonyms' => ['sometimes', 'array'], 'translations' => ['sometimes', 'array'], 'position' => ['sometimes', 'integer', 'min:0']]);
        return new TermResource($service->addTerm($model, $data['name'], $data['slug'] ?? null, $data['parent_id'] ?? null, $data['synonyms'] ?? [], $data['translations'] ?? [], $data['position'] ?? null));
    }

    public function moveTerm(Request $request, int $term, TaxonomyQuery $query, TaxonomyService $service): TermResource
    {
        $model = $query->term($term);
        if (! $model) throw new NotFoundHttpException;
        $data = $request->validate(['parent_id' => ['nullable', 'integer'], 'position' => ['sometimes', 'integer', 'min:0']]);
        return new TermResource($service->move($model, $data['parent_id'] ?? null, $data['position'] ?? null));
    }
}
