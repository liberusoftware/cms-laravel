<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;
use Liberu\Cms\Notifications\Channels\ChannelManager;
use Liberu\Cms\Notifications\Enums\NotificationStatus;
use Liberu\Cms\Notifications\Messages\NotificationMessage;
use Liberu\Cms\Notifications\Models\NotificationLog;
use Throwable;

/**
 * Delivers a resolved notification on its channel. Queued so a slow channel
 * (e.g. SMTP) never blocks the request that triggered the event.
 *
 * Hardened for at-least-once delivery: it retries with backoff, and an
 * idempotency guard keyed on the {@see NotificationLog} row skips re-sending a
 * notification that already went out (the one place a duplicate is user-visible —
 * a duplicate email). When it finally fails it marks the row `failed` and emits a
 * `notification.failed` metric.
 *
 * The guard marks the row `sent` after delivery, so it covers the common retry
 * path (a job re-run after a completed attempt). It does not make delivery
 * exactly-once: a worker that dies between the send and the mark could re-send on
 * retry. Closing that window needs a transactional outbox, which is out of scope
 * for a single job (it would be exactly the generic dedup machinery this ticket
 * deliberately avoids).
 */
final class SendNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const int DEFAULT_TRIES = 3;

    /**
     * @var array<int, int>
     */
    private const array DEFAULT_BACKOFF = [10, 30, 60];

    public int $tries;

    public function __construct(
        public readonly NotificationMessage $message,
        public readonly int $notificationLogId,
    ) {
        $tries = config('cms-notifications.queue.tries', self::DEFAULT_TRIES);
        $this->tries = is_numeric($tries) ? max(1, (int) $tries) : self::DEFAULT_TRIES;
    }

    /**
     * Seconds to wait before each retry (the last value repeats for further
     * attempts).
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        $configured = config('cms-notifications.queue.backoff', self::DEFAULT_BACKOFF);

        if (! is_array($configured)) {
            return self::DEFAULT_BACKOFF;
        }

        $seconds = [];

        foreach ($configured as $value) {
            if (is_numeric($value)) {
                $seconds[] = max(0, (int) $value);
            }
        }

        return $seconds === [] ? self::DEFAULT_BACKOFF : $seconds;
    }

    public function handle(ChannelManager $channels): void
    {
        $log = NotificationLog::find($this->notificationLogId);

        if ($log?->status === NotificationStatus::Sent) {
            return;
        }

        $channels->channel($this->message->channel)?->send($this->message);

        $log?->update(['status' => NotificationStatus::Sent]);
    }

    /**
     * Runs once the job has exhausted its retries: record the terminal failure
     * on the log row and, when the metrics seam is bound, count it.
     */
    public function failed(Throwable $exception): void
    {
        NotificationLog::find($this->notificationLogId)?->update(['status' => NotificationStatus::Failed]);

        $container = Container::getInstance();

        if ($container->bound(MetricsRecorderInterface::class)) {
            $container->make(MetricsRecorderInterface::class)
                ->increment('notification.failed', tags: ['channel' => $this->message->channel]);
        }
    }
}
