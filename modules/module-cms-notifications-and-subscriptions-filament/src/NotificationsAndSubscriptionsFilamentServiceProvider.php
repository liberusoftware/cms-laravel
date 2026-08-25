<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptionsFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\NotificationsAndSubscriptionsFilament\Resources\SubscriptionResource;

final class NotificationsAndSubscriptionsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('notifications-and-subscriptions', SubscriptionResource::class);
        }
    }
}
