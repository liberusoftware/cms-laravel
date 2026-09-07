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
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint(
                'content-entities',
                new ApiEndpoint('entities/{type}', ContentEntitiesController::class, 'index', 'entities.index'),
            );
            $registry->registerEndpoint(
                'content-entities',
                new ApiEndpoint('entities/{type}/{slug}', ContentEntitiesController::class, 'show', 'entities.show'),
            );
            $registry->registerEndpoint(
                'content-entities',
                new ApiEndpoint('cms/content-entities', ContentEntitiesController::class, 'store', 'cms.content-entities.create', 'POST', ['abilities:content:write']),
            );
            $registry->registerEndpoint(
                'content-entities',
                new ApiEndpoint('cms/content-entities/{id}', ContentEntitiesController::class, 'update', 'cms.content-entities.update', 'PATCH', ['abilities:content:write']),
            );
            $registry->registerEndpoint(
                'content-entities',
                new ApiEndpoint('cms/content-entities/{id}', ContentEntitiesController::class, 'destroy', 'cms.content-entities.delete', 'DELETE', ['abilities:content:write']),
            );
            $registry->registerEndpoint(
                'content-entities',
                new ApiEndpoint('entities/{type}/{id}/clone', ContentEntitiesController::class, 'cloneEntity', 'entities.clone', 'POST', ['abilities:content:write']),
            );
            $registry->registerEndpoint('content-entities', new ApiEndpoint('content/{type}', ContentEntitiesController::class, 'legacyIndex', 'content.index'));
            $registry->registerEndpoint('content-entities', new ApiEndpoint('content/{type}/{slug}', ContentEntitiesController::class, 'legacyShow', 'content.show'));
            $registry->registerEndpoint('content-entities', new ApiEndpoint('content-entries', ContentEntitiesController::class, 'legacyStore', 'content-entries.store', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('content-entities', new ApiEndpoint('content-entries/{id}', ContentEntitiesController::class, 'legacyUpdate', 'content-entries.update', 'PUT', ['abilities:content:write']));
            $registry->registerEndpoint('content-entities', new ApiEndpoint('content-entries/{id}', ContentEntitiesController::class, 'destroy', 'content-entries.destroy', 'DELETE', ['abilities:content:write']));
        }
    }
}
