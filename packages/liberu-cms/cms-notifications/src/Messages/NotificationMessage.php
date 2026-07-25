<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Messages;

/**
 * A resolved notification ready for delivery on a channel. Serializable so it
 * can ride the queue.
 */
final readonly class NotificationMessage
{
    /**
     * @param  array<int, string>  $to
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public string $channel,
        public array $to,
        public string $subject,
        public string $body,
        public string $event,
        public int|string|null $teamId = null,
        public array $context = [],
    ) {}
}
