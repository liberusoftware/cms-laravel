<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContentApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\EventsContentApi\Http\EventsContentController;

final class EventsContentApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('events-content-api', new ApiEndpoint('cms/events-content', EventsContentController::class, 'index', 'cms.events-content.index'));
        $registry->registerEndpoint('events-content-api', new ApiEndpoint('cms/events-content/{key}', EventsContentController::class, 'show', 'cms.events-content.show'));
        $registry->registerEndpoint('events-content-api', new ApiEndpoint('cms/events-content', EventsContentController::class, 'store', 'cms.events-content.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('events-content-api', new ApiEndpoint('cms/events-content/{key}/publish', EventsContentController::class, 'publish', 'cms.events-content.publish', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('events-content-api', new ApiEndpoint('cms/events-content/{key}/archive', EventsContentController::class, 'archive', 'cms.events-content.archive', 'POST', ['abilities:content:write']));
    }
}
