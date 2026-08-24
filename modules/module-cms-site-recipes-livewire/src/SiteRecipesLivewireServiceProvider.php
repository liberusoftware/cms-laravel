<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\SiteRecipesLivewire\Livewire\RecipePreview;
use Livewire\Livewire;

final class SiteRecipesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-site-recipes.recipe-preview', RecipePreview::class);
    }
}
