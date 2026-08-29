<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipesApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\SiteRecipesApi\Http\SiteRecipesController;

final class SiteRecipesApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('site-recipes-api', new ApiEndpoint('cms/site-recipes', SiteRecipesController::class, 'index', 'cms.site-recipes.index'));
            $r->registerEndpoint('site-recipes-api', new ApiEndpoint('cms/site-recipes', SiteRecipesController::class, 'create', 'cms.site-recipes.create', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-recipes-api', new ApiEndpoint('cms/site-recipes/{recipe}', SiteRecipesController::class, 'show', 'cms.site-recipes.show'));
            $r->registerEndpoint('site-recipes-api', new ApiEndpoint('cms/site-recipes/{recipe}', SiteRecipesController::class, 'update', 'cms.site-recipes.update', 'PATCH', ['abilities:content:write']));
            $r->registerEndpoint('site-recipes-api', new ApiEndpoint('cms/site-recipes/{recipe}/versions', SiteRecipesController::class, 'version', 'cms.site-recipes.version', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-recipes-api', new ApiEndpoint('cms/site-recipes/{recipe}/publish', SiteRecipesController::class, 'publish', 'cms.site-recipes.publish', 'POST', ['abilities:content:publish']));
            $r->registerEndpoint('site-recipes-api', new ApiEndpoint('cms/site-recipes/{recipe}/archive', SiteRecipesController::class, 'archive', 'cms.site-recipes.archive', 'POST', ['abilities:content:write']));
        }
    }
}
