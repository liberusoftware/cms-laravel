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
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('theme-integration-api', new ApiEndpoint('cms/theme-integration', ThemeIntegrationController::class, 'show', 'cms.theme-integration.show'));
        }
    }
}
