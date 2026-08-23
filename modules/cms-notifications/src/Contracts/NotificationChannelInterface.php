<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Contracts;

use Liberu\Cms\Notifications\Messages\NotificationMessage;

/**
 * A delivery channel for notifications (mail, log, webhook, …). Channels are
 * keyed; a subscription names the channel it wants by key.
 */
interface NotificationChannelInterface
{
    public function key(): string;

    public function send(NotificationMessage $message): void;
}
