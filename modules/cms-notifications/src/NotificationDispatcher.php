<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications;

use Liberu\Cms\Notifications\Jobs\SendNotification;
use Liberu\Cms\Notifications\Messages\NotificationMessage;
use Liberu\Cms\Notifications\Models\NotificationLog;

/**
 * Turns a CMS event into notifications: for each configured subscription on the
 * event, it records an audit log row and queues delivery on the named channel.
 */
final readonly class NotificationDispatcher
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function dispatch(string $event, int|string|null $teamId, array $context): void
    {
        foreach ($this->subscriptionsFor($event) as $subscription) {
            $channel = $this->stringOr($subscription['channel'] ?? null, 'log');
            $subject = $this->stringOr($subscription['subject'] ?? null, 'Notification');
            $recipients = $this->recipients($subscription['to'] ?? null);

            $message = new NotificationMessage(
                channel: $channel,
                to: $recipients,
                subject: $subject,
                body: $this->body($event, $context),
                event: $event,
                teamId: $teamId,
                context: $context,
            );

            $log = NotificationLog::create([
                'event' => $event,
                'channel' => $channel,
                'recipient' => $recipients === [] ? null : implode(', ', $recipients),
                'team_id' => is_int($teamId) ? $teamId : null,
                'context' => $context,
            ]);

            SendNotification::dispatch($message, $log->id);
        }
    }

    /**
     * @return list<array<array-key, mixed>>
     */
    private function subscriptionsFor(string $event): array
    {
        $all = config('cms-notifications.subscriptions');

        if (! is_array($all)) {
            return [];
        }

        $subscriptions = $all[$event] ?? [];

        if (! is_array($subscriptions)) {
            return [];
        }

        $result = [];

        foreach ($subscriptions as $subscription) {
            if (is_array($subscription)) {
                $result[] = $subscription;
            }
        }

        return $result;
    }

    private function stringOr(mixed $value, string $default): string
    {
        return is_string($value) && $value !== '' ? $value : $default;
    }

    /**
     * @return array<int, string>
     */
    private function recipients(mixed $to): array
    {
        if (is_string($to) && $to !== '') {
            return [$to];
        }

        if (! is_array($to)) {
            return [];
        }

        $recipients = [];

        foreach ($to as $recipient) {
            if (is_string($recipient) && $recipient !== '') {
                $recipients[] = $recipient;
            }
        }

        return $recipients;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function body(string $event, array $context): string
    {
        $json = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return 'Event: '.$event.PHP_EOL.(is_string($json) ? $json : '{}');
    }
}
