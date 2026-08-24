<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwaFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\OfflineAndPwaFilament\Resources\PwaConfigurationResource;

final class OfflineAndPwaFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('offline-and-pwa', PwaConfigurationResource::class);
        }
    }
}
