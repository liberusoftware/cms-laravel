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
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/sites', SiteFactoryController::class, 'store', 'cms.site-factory.store', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/templates', SiteFactoryController::class, 'template', 'cms.site-factory.template', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/sites/{site}/suspend', SiteFactoryController::class, 'suspend', 'cms.site-factory.suspend', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/sites/{site}/archive', SiteFactoryController::class, 'archive', 'cms.site-factory.archive', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/sites/{site}/clone', SiteFactoryController::class, 'clone', 'cms.site-factory.clone', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/domains/{domain}/verify', SiteFactoryController::class, 'verifyDomain', 'cms.site-factory.domain.verify', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/sites/{site}/teardown', SiteFactoryController::class, 'teardown', 'cms.site-factory.teardown', 'DELETE', ['abilities:content:write']));
        }
    }
}
