<?php

declare(strict_types=1);

namespace Liberu\Cms\NotificationsAndSubscriptions;

use Liberu\Cms\Core\Module\AbstractModule;

final class NotificationsAndSubscriptionsModule extends AbstractModule
{
    public function key(): string
    {
        return 'notifications-and-subscriptions';
    }

    public function name(): string
    {
        return 'Notifications and Subscriptions';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
