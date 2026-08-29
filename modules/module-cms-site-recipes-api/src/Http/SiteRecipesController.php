<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipesApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\SiteRecipes\Queries\SiteRecipeQuery;
use Liberu\Cms\SiteRecipes\Services\SiteRecipeService;
use Liberu\Cms\SiteRecipesApi\Http\Resources\SiteRecipeResource;

final class SiteRecipesController
{
    public function index(Request $request, SiteRecipeQuery $query): JsonResponse
    {
        $page = $query->recipes($request->integer('per_page', 15), (bool) $request->boolean('published_only'));

        return response()->json(['data' => array_map(SiteRecipeResource::make(...), $page->items()), 'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()]]);
    }

    public function show(string $recipe, SiteRecipeQuery $query, SiteRecipeService $service): JsonResponse
    {
        $model = $query->find($recipe, true);
        abort_unless($model, 404);

        return response()->json(['data' => SiteRecipeResource::make($model), 'export' => $service->export($model)]);
    }

    public function create(Request $request, SiteRecipeService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:100'], 'name' => ['required', 'string', 'max:255'], 'description' => ['sometimes', 'nullable', 'string']]);

        return response()->json(['data' => SiteRecipeResource::make($service->create($data['key'], $data['name'], $data['description'] ?? null))], 201);
    }

    public function update(Request $request, string $recipe, SiteRecipeQuery $query, SiteRecipeService $service): JsonResponse
    {
        $model = $query->find($recipe);
        abort_unless($model, 404);
        $data = $request->validate(['key' => ['sometimes', 'string', 'max:100'], 'name' => ['sometimes', 'string', 'max:255'], 'description' => ['sometimes', 'nullable', 'string'], 'status' => ['sometimes', 'in:draft,published,archived']]);

        return response()->json(['data' => SiteRecipeResource::make($service->update($model, $data))]);
    }

    public function version(Request $request, string $recipe, SiteRecipeQuery $query, SiteRecipeService $service): JsonResponse
    {
        $model = $query->find($recipe);
        abort_unless($model, 404);
        $data = $request->validate(['bundle' => ['required', 'array']]);

        return response()->json(['data' => $service->version($model, $data['bundle'])], 201);
    }

    public function publish(string $recipe, SiteRecipeQuery $query, SiteRecipeService $service): JsonResponse
    {
        $model = $query->find($recipe);
        abort_unless($model, 404);

        return response()->json(['data' => SiteRecipeResource::make($service->publish($model))]);
    }

    public function archive(string $recipe, SiteRecipeQuery $query, SiteRecipeService $service): JsonResponse
    {
        $model = $query->find($recipe);
        abort_unless($model, 404);

        return response()->json(['data' => SiteRecipeResource::make($service->archive($model))]);
    }
}
