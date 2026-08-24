<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgetsFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\RegionsAndWidgetsFilament\Resources\RegionResource;

final class RegionsAndWidgetsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('regions-and-widgets', RegionResource::class);
        }
    }
}
