<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Liberu\Cms\Notifications\Channels\ChannelManager;
use Liberu\Cms\Notifications\Messages\NotificationMessage;

/**
 * Delivers a resolved notification on its channel. Queued so a slow channel
 * (e.g. SMTP) never blocks the request that triggered the event.
 */
final class SendNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly NotificationMessage $message) {}

    public function handle(ChannelManager $channels): void
    {
        $channels->channel($this->message->channel)?->send($this->message);
    }
}
