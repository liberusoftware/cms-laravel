<?php

declare(strict_types=1);

namespace Liberu\Cms\NavigationFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\NavigationFilament\Resources\NavigationItemResource;
use Liberu\Cms\NavigationFilament\Resources\NavigationResource;

final class NavigationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(AdminResourceRegistryInterface::class)) {
            return;
        }

        $registry = $this->app->make(AdminResourceRegistryInterface::class);
        $registry->registerResource('navigation', NavigationResource::class);
        $registry->registerResource('navigation', NavigationItemResource::class);
    }
}
