<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDeliveryFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\WebDeliveryFilament\Resources\DeliveryRouteResource;

final class WebDeliveryFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) $this->app->make(AdminResourceRegistryInterface::class)->registerResource('web-delivery', DeliveryRouteResource::class);
    }
}
