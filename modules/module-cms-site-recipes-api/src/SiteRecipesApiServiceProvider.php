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
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('site-recipes-api', new ApiEndpoint('cms/site-recipes/{recipe}', SiteRecipesController::class, 'show', 'cms.site-recipes.show'));
        }
    }
}
