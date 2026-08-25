<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipes;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\SiteRecipes\Queries\SiteRecipeQuery;
use Liberu\Cms\SiteRecipes\Services\SiteRecipeService;

final class SiteRecipesServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new SiteRecipesModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(SiteRecipeService::class);
        $this->app->singleton(SiteRecipeQuery::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('site-recipes', 'Site Recipes', AccessScope::Module, ['view', 'create', 'update', 'publish', 'archive', 'export']));
        }
    }
}
