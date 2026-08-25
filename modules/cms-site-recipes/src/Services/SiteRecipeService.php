<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipes\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\SiteRecipes\Models\SiteRecipe;
use Liberu\Cms\SiteRecipes\Models\SiteRecipeVersion;

final class SiteRecipeService
{
    public function create(string $key, string $name, ?string $description = null, ?int $teamId = null): SiteRecipe
    {
        if (trim($key) === '' || trim($name) === '') {
            throw ValidationException::withMessages(['name' => 'Recipe key and name are required.']);
        }

        return SiteRecipe::query()->create(['key' => Str::slug($key), 'name' => $name, 'description' => $description, 'status' => 'draft', 'team_id' => $teamId]);
    }

    public function version(SiteRecipe $recipe, array $bundle, ?int $authorId = null): SiteRecipeVersion
    {
        $this->validateBundle($bundle);
        $payload = array_intersect_key($bundle, array_flip(['modules', 'configuration', 'content_types', 'workflows', 'menus', 'blocks', 'themes', 'starter_content']));
        $version = ((int) $recipe->versions()->max('version')) + 1;

        return $recipe->versions()->create($payload + ['version' => $version, 'checksum' => hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR)), 'author_id' => $authorId]);
    }

    public function publish(SiteRecipe $recipe): SiteRecipe
    {
        if ($recipe->versions()->doesntExist()) {
            throw ValidationException::withMessages(['recipe' => 'A recipe needs a version before publishing.']);
        } $recipe->forceFill(['status' => 'published'])->save();

        return $recipe->fresh();
    }

    public function archive(SiteRecipe $recipe): SiteRecipe
    {
        return tap($recipe)->update(['status' => 'archived']);
    }

    public function export(SiteRecipe $recipe): array
    {
        $version = $recipe->versions()->latest('version')->firstOrFail();

        return ['key' => $recipe->key, 'name' => $recipe->name, 'version' => $version->version, 'checksum' => $version->checksum, 'bundle' => $version->only(['modules', 'configuration', 'content_types', 'workflows', 'menus', 'blocks', 'themes', 'starter_content'])];
    }

    private function validateBundle(array $bundle): void
    {
        foreach (['modules', 'configuration', 'content_types', 'workflows', 'menus', 'blocks', 'themes', 'starter_content'] as $key) {
            if (isset($bundle[$key]) && ! is_array($bundle[$key])) {
                throw ValidationException::withMessages([$key => 'Recipe bundle sections must be arrays.']);
            }
        }
    }
}
