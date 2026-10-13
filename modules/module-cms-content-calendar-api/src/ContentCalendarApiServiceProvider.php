<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendarApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentCalendarApi\Http\ContentCalendarController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentCalendarApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('content-calendar-api', new ApiEndpoint('cms/content-calendar/items', ContentCalendarController::class, 'index', 'cms.content-calendar.items.index'));
        $registry->registerEndpoint('content-calendar-api', new ApiEndpoint('cms/content-calendar/campaigns', ContentCalendarController::class, 'campaign', 'cms.content-calendar.campaigns.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-calendar-api', new ApiEndpoint('cms/content-calendar/items', ContentCalendarController::class, 'store', 'cms.content-calendar.items.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-calendar-api', new ApiEndpoint('cms/content-calendar/items/{item}/schedule', ContentCalendarController::class, 'reschedule', 'cms.content-calendar.items.reschedule', 'PATCH', ['abilities:content:write']));
    }
}
