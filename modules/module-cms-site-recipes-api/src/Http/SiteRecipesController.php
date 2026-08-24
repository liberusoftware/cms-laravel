<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipesApi\Http;

use Illuminate\Http\JsonResponse;
use Liberu\Cms\SiteRecipes\Models\SiteRecipe;
use Liberu\Cms\SiteRecipes\Services\SiteRecipeService;

final class SiteRecipesController
{
    public function show(string $recipe, SiteRecipeService $service): JsonResponse
    {
        return response()->json(['data' => $service->export(SiteRecipe::query()->where('key', $recipe)->where('status', 'published')->firstOrFail())]);
    }
}
