<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\WebDelivery\Actions\WebDeliveryService;
use Liberu\Cms\WebDelivery\Queries\DeliveryRouteQuery;
use Liberu\Cms\WebDelivery\Support\EdgeInvalidationRegistry;
use Liberu\Cms\WebDelivery\Support\NullEdgeInvalidationRegistry;

final class WebDeliveryServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new WebDeliveryModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/web-delivery.php', 'web-delivery');
        $this->app->singleton(EdgeInvalidationRegistry::class, NullEdgeInvalidationRegistry::class);
        $this->app->singleton(WebDeliveryService::class);
        $this->app->singleton(DeliveryRouteQuery::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('web-delivery', 'Web Delivery', AccessScope::Content, ['view', 'create', 'update', 'delete', 'preview', 'invalidate']));
        }
    }
}
