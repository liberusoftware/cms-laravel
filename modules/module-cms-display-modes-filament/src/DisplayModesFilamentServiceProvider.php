<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModesFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\DisplayModesFilament\Resources\DisplayModeResource;

final class DisplayModesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('display-modes', DisplayModeResource::class);
        }
    }
}
