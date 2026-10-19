<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentFederationApi\Http\ContentFederationController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentFederationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('content-federation-api', new ApiEndpoint('cms/content-federation/sources', ContentFederationController::class, 'index', 'cms.content-federation.sources.index'));
        $registry->registerEndpoint('content-federation-api', new ApiEndpoint('cms/content-federation/sources', ContentFederationController::class, 'store', 'cms.content-federation.sources.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-federation-api', new ApiEndpoint('cms/content-federation/sources/{source}/references', ContentFederationController::class, 'ingest', 'cms.content-federation.references.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-federation-api', new ApiEndpoint('cms/content-federation/sources/{source}/references/{type}/{key}', ContentFederationController::class, 'fallback', 'cms.content-federation.references.show'));
    }
}
