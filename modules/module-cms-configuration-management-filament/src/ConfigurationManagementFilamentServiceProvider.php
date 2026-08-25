<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagementFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ConfigurationManagementFilament\Resources\ConfigurationReleaseResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ConfigurationManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('configuration-management', ConfigurationReleaseResource::class);
        }
    }
}
