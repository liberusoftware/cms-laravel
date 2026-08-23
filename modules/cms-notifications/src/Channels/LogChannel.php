<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Channels;

use Liberu\Cms\Notifications\Contracts\NotificationChannelInterface;
use Liberu\Cms\Notifications\Messages\NotificationMessage;
use Psr\Log\LoggerInterface;

/**
 * Writes a notification to the application log. Useful as a default channel and
 * for local development.
 */
final readonly class LogChannel implements NotificationChannelInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function key(): string
    {
        return 'log';
    }

    public function send(NotificationMessage $message): void
    {
        $this->logger->info('[cms-notifications] '.$message->subject, [
            'event' => $message->event,
            'to' => $message->to,
            'context' => $message->context,
        ]);
    }
}
