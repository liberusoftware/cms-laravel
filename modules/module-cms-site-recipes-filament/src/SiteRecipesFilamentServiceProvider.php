<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipesFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\SiteRecipesFilament\Resources\SiteRecipeResource;

final class SiteRecipesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('site-recipes', SiteRecipeResource::class);
        }
    }
}
