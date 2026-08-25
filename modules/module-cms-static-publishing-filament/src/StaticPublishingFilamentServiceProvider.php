<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishingFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\StaticPublishingFilament\Resources\StaticBuildResource;

final class StaticPublishingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('static-publishing', StaticBuildResource::class);
        }
    }
}
