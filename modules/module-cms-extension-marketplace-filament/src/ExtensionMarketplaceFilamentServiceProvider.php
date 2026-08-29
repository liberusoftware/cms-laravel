<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplaceFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\ExtensionMarketplaceFilament\Resources\ExtensionListingResource;

final class ExtensionMarketplaceFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('extension-marketplace', ExtensionListingResource::class);
        }
    }
}
