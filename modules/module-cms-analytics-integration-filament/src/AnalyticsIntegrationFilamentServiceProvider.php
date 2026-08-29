<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegrationFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AnalyticsIntegrationFilament\Resources\AnalyticsEventResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class AnalyticsIntegrationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('analytics-integration', AnalyticsEventResource::class);
        }
    }
}
