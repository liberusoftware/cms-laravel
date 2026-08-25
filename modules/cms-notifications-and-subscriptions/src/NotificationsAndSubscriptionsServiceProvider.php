<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptions;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\NotificationsAndSubscriptions\Services\SubscriptionService;

final class NotificationsAndSubscriptionsServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new NotificationsAndSubscriptionsModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(SubscriptionService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('notifications-and-subscriptions', 'Notifications and Subscriptions', AccessScope::Module, ['view', 'create', 'update', 'delete']));
        }
    }
}
