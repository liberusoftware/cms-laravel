<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Channels;

use Liberu\Cms\Notifications\Contracts\NotificationChannelInterface;

/**
 * Resolves a notification channel by its key. Built-in channels are registered
 * at boot; a host can bind more before use.
 */
final class ChannelManager
{
    /**
     * @var array<string, NotificationChannelInterface>
     */
    private array $channels = [];

    /**
     * @param  iterable<int, NotificationChannelInterface>  $channels
     */
    public function __construct(iterable $channels = [])
    {
        foreach ($channels as $channel) {
            $this->register($channel);
        }
    }

    public function register(NotificationChannelInterface $channel): void
    {
        $this->channels[$channel->key()] = $channel;
    }

    public function channel(string $key): ?NotificationChannelInterface
    {
        return $this->channels[$key] ?? null;
    }
}
