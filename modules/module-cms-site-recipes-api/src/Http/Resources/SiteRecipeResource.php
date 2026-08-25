<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipesApi\Http\Resources;

use Liberu\Cms\SiteRecipes\Models\SiteRecipe;

final class SiteRecipeResource
{
    /** @return array<string,mixed> */
    public static function make(SiteRecipe $recipe): array
    {
        $version = $recipe->versions->first();

        return ['id' => (string) $recipe->getKey(), 'type' => 'cms-site-recipe', 'key' => $recipe->key, 'name' => $recipe->name, 'description' => $recipe->description, 'status' => $recipe->status, 'version' => $version?->version, 'checksum' => $version?->checksum];
    }
}
