<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\ThemeIntegrationApi\Http\ThemeIntegrationController;

final class ThemeIntegrationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('theme-integration-api', new ApiEndpoint('cms/theme-integration', ThemeIntegrationController::class, 'index', 'cms.theme-integration.index'));
            $registry->registerEndpoint('theme-integration-api', new ApiEndpoint('cms/theme-integration', ThemeIntegrationController::class, 'create', 'cms.theme-integration.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('theme-integration-api', new ApiEndpoint('cms/theme-integration/resolve', ThemeIntegrationController::class, 'show', 'cms.theme-integration.resolve'));
            $registry->registerEndpoint('theme-integration-api', new ApiEndpoint('cms/theme-integration/components', ThemeIntegrationController::class, 'components', 'cms.theme-integration.components'));
            $registry->registerEndpoint('theme-integration-api', new ApiEndpoint('cms/theme-integration/components', ThemeIntegrationController::class, 'registerComponent', 'cms.theme-integration.components.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('theme-integration-api', new ApiEndpoint('cms/theme-integration/{binding}', ThemeIntegrationController::class, 'binding', 'cms.theme-integration.binding'));
            $registry->registerEndpoint('theme-integration-api', new ApiEndpoint('cms/theme-integration/{binding}', ThemeIntegrationController::class, 'update', 'cms.theme-integration.update', 'PATCH', ['abilities:content:write']));
            $registry->registerEndpoint('theme-integration-api', new ApiEndpoint('cms/theme-integration/{binding}', ThemeIntegrationController::class, 'delete', 'cms.theme-integration.delete', 'DELETE', ['abilities:content:write']));
        }
    }
}
