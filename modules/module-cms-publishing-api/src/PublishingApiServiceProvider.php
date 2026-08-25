<?php

declare(strict_types=1);

namespace Liberu\Cms\PublishingApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\PublishingApi\Http\ReleaseController;

final class PublishingApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('publishing-api', new ApiEndpoint('cms/publishing/{key}', ReleaseController::class, 'show', 'cms.publishing.show'));
            $registry->registerEndpoint('publishing-api', new ApiEndpoint('cms/publishing', ReleaseController::class, 'store', 'cms.publishing.store', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('publishing-api', new ApiEndpoint('cms/publishing/{key}/schedule', ReleaseController::class, 'schedule', 'cms.publishing.schedule', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('publishing-api', new ApiEndpoint('cms/publishing/{key}/publish', ReleaseController::class, 'publish', 'cms.publishing.publish', 'POST', ['abilities:content:publish']));
            $registry->registerEndpoint('publishing-api', new ApiEndpoint('cms/publishing/{key}/unpublish', ReleaseController::class, 'unpublish', 'cms.publishing.unpublish', 'POST', ['abilities:content:unpublish']));
            $registry->registerEndpoint('publishing-api', new ApiEndpoint('cms/publishing/{key}/archive', ReleaseController::class, 'archive', 'cms.publishing.archive', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('publishing-api', new ApiEndpoint('cms/publishing/process-due', ReleaseController::class, 'processDue', 'cms.publishing.process-due', 'POST', ['abilities:content:publish']));
        }
    }
}
