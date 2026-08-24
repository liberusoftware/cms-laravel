<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactoryApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\SiteFactoryApi\Http\SiteFactoryController;

final class SiteFactoryApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/sites', SiteFactoryController::class, 'store', 'cms.site-factory.store', 'POST'));
        }
    }
}
