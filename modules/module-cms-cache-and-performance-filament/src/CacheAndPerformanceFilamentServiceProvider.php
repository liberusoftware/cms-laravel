<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformanceFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CacheAndPerformanceFilament\Resources\CacheEntryResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class CacheAndPerformanceFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('cache-and-performance', CacheEntryResource::class);
        }
    }
}
