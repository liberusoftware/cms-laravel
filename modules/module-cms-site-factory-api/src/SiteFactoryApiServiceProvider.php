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
            $read = ['abilities:site-factory:view'];
            $write = ['abilities:site-factory:update'];
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory', SiteFactoryController::class, 'index', 'cms.site.factory.list', 'GET', $read));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory', SiteFactoryController::class, 'store', 'cms.site.factory.create', 'POST', ['abilities:site-factory:create']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/{id}', SiteFactoryController::class, 'show', 'cms.site.factory.get', 'GET', $read));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/{id}', SiteFactoryController::class, 'update', 'cms.site.factory.update', 'PATCH', $write));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/{id}', SiteFactoryController::class, 'destroy', 'cms.site.factory.delete', 'DELETE', $write));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/{id}/clone', SiteFactoryController::class, 'cloneById', 'cms.site.factory.clone', 'POST', $write));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/{id}/suspend', SiteFactoryController::class, 'suspendById', 'cms.site.factory.suspend', 'POST', $write));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/{id}/archive', SiteFactoryController::class, 'archiveById', 'cms.site.factory.archive', 'POST', $write));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/{id}/teardown', SiteFactoryController::class, 'teardownById', 'cms.site.factory.teardown', 'DELETE', ['abilities:site-factory:teardown']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/templates', SiteFactoryController::class, 'templates', 'cms.site.factory.templates', 'GET', $read));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/templates', SiteFactoryController::class, 'template', 'cms.site-factory.template', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/sites/{site}/suspend', SiteFactoryController::class, 'suspend', 'cms.site-factory.suspend', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/sites/{site}/archive', SiteFactoryController::class, 'archive', 'cms.site-factory.archive', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/sites/{site}/clone', SiteFactoryController::class, 'clone', 'cms.site-factory.clone', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/domains/{domain}/verify', SiteFactoryController::class, 'verifyDomain', 'cms.site-factory.domain.verify', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('site-factory-api', new ApiEndpoint('cms/site-factory/sites/{site}/teardown', SiteFactoryController::class, 'teardown', 'cms.site-factory.teardown', 'DELETE', ['abilities:content:write']));
        }
    }
}
