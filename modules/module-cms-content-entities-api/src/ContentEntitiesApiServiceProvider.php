<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentEntitiesApi\Http\Controllers\ContentEntitiesController;
use Liberu\Cms\ContentTypes\Queries\PublishedEntityQuery;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentEntitiesApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PublishedEntityQuery::class);

        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint(
                'content-entities',
                new ApiEndpoint('entities/{type}', ContentEntitiesController::class, 'index', 'entities.index'),
            );
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint(
                'content-entities',
                new ApiEndpoint('entities/{type}/{slug}', ContentEntitiesController::class, 'show', 'entities.show'),
            );
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint(
                'content-entities',
                new ApiEndpoint('entities/{type}/{id}/clone', ContentEntitiesController::class, 'cloneEntity', 'entities.clone', 'POST', ['abilities:content:write']),
            );
        }
    }
}
