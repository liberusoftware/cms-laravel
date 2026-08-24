<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\ThemeMarketplaceFilament\Resources\MarketplaceThemeResource;

final class ThemeMarketplaceFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('theme-marketplace', MarketplaceThemeResource::class);
        }
    }
}
