<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegrationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AnalyticsIntegrationApi\Http\AnalyticsIntegrationController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class AnalyticsIntegrationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('analytics-integration-api', new ApiEndpoint('cms/analytics-integration/events', AnalyticsIntegrationController::class, 'record', 'cms.analytics-integration.events.record', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('analytics-integration-api', new ApiEndpoint('cms/analytics-integration/mappings', AnalyticsIntegrationController::class, 'mapping', 'cms.analytics-integration.mappings.create', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('analytics-integration-api', new ApiEndpoint('cms/analytics-integration/dashboard', AnalyticsIntegrationController::class, 'dashboard', 'cms.analytics-integration.dashboard'));
    }
}
