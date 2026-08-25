<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipesLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\SiteRecipes\Models\SiteRecipe;
use Liberu\Cms\SiteRecipes\Services\SiteRecipeService;
use Livewire\Component;

final class RecipePreview extends Component
{
    public string $recipeKey = '';

    public function render(SiteRecipeService $service): View
    {
        $recipe = $this->recipeKey === '' ? null : SiteRecipe::query()->where('key', $this->recipeKey)->where('status', 'published')->first();

        return view('cms-site-recipes-livewire::recipe-preview', ['export' => $recipe ? $service->export($recipe) : null]);
    }
}
