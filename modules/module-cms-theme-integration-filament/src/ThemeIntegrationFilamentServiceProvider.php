<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\ThemeIntegrationFilament\Resources\ThemeBindingResource;

final class ThemeIntegrationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('theme-integration', ThemeBindingResource::class);
        }
    }
}
