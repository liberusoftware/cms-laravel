<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\CoreFilament\Resources\ChannelResource;
use Liberu\Cms\CoreFilament\Resources\SiteResource;

final class CoreFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $registry = $this->app->make(AdminResourceRegistryInterface::class);
            $registry->registerResource('core', SiteResource::class);
            $registry->registerResource('core', ChannelResource::class);
        }
    }
}
