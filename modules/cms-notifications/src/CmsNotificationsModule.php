<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * Notifications. Listens for CMS events and delivers configured notifications
 * through channels (mail, log, …), queued. Consumes only contracts and the core
 * module system, so removing it just stops notifications firing.
 */
final class CmsNotificationsModule extends AbstractModule
{
    public function key(): string
    {
        return 'notifications';
    }

    public function name(): string
    {
        return 'Notifications';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
