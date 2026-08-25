<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishingApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\StaticPublishingApi\Http\StaticPublishingController;

final class StaticPublishingApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('static-publishing-api', new ApiEndpoint('cms/static-publishing/builds', StaticPublishingController::class, 'index', 'cms.static-publishing.index'));
            $r->registerEndpoint('static-publishing-api', new ApiEndpoint('cms/static-publishing/builds', StaticPublishingController::class, 'store', 'cms.static-publishing.store', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('static-publishing-api', new ApiEndpoint('cms/static-publishing/builds/{build}', StaticPublishingController::class, 'show', 'cms.static-publishing.show'));
            $r->registerEndpoint('static-publishing-api', new ApiEndpoint('cms/static-publishing/builds/{build}/invalidate', StaticPublishingController::class, 'invalidate', 'cms.static-publishing.invalidate', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('static-publishing-api', new ApiEndpoint('cms/static-publishing/builds/{build}/rollback', StaticPublishingController::class, 'rollback', 'cms.static-publishing.rollback', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('static-publishing-api', new ApiEndpoint('cms/static-publishing/builds/{build}/diagnostics', StaticPublishingController::class, 'diagnostics', 'cms.static-publishing.diagnostics'));
            $r->registerEndpoint('static-publishing-api', new ApiEndpoint('cms/static-publishing/builds/{build}/deploy', StaticPublishingController::class, 'deploy', 'cms.static-publishing.deploy', 'POST', ['abilities:content:write']));
        }
    }
}
