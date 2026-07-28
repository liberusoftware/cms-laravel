<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;
use Liberu\Cms\Notifications\Channels\ChannelManager;
use Liberu\Cms\Notifications\Contracts\NotificationChannelInterface;
use Liberu\Cms\Notifications\Enums\NotificationStatus;
use Liberu\Cms\Notifications\Jobs\SendNotification;
use Liberu\Cms\Notifications\Messages\NotificationMessage;
use Liberu\Cms\Notifications\Models\NotificationLog;
use Tests\Fixtures\RecordingMetricsRecorder;

uses(RefreshDatabase::class);

function notificationMessage(string $channel = 'test'): NotificationMessage
{
    return new NotificationMessage(
        channel: $channel,
        to: ['ops@example.com'],
        subject: 'Subject',
        body: 'Body',
        event: 'forms.submitted',
    );
}

function pendingLog(string $channel = 'test'): NotificationLog
{
    return NotificationLog::create([
        'event' => 'forms.submitted',
        'channel' => $channel,
        'status' => NotificationStatus::Pending,
    ]);
}

/**
 * A channel that counts how many times it was asked to send.
 */
function countingChannel(string $key = 'test'): NotificationChannelInterface
{
    return new class($key) implements NotificationChannelInterface
    {
        public int $sends = 0;

        public function __construct(private string $key) {}

        public function key(): string
        {
            return $this->key;
        }

        public function send(NotificationMessage $message): void
        {
            $this->sends++;
        }
    };
}

it('honours a configurable tries and backoff', function (): void {
    $job = new SendNotification(notificationMessage(), 1);

    expect($job->tries)->toBe(3)
        ->and($job->backoff())->toBe([10, 30, 60]);

    config(['cms-notifications.queue.tries' => 5, 'cms-notifications.queue.backoff' => [5, 15]]);

    $override = new SendNotification(notificationMessage(), 1);

    expect($override->tries)->toBe(5)
        ->and($override->backoff())->toBe([5, 15]);
});

it('does not re-send a notification that already delivered on retry', function (): void {
    $log = pendingLog();
    $channel = countingChannel();
    $channels = new ChannelManager([$channel]);

    $job = new SendNotification(notificationMessage(), $log->id);

    $job->handle($channels);
    $job->handle($channels); // a retry after the successful delivery

    expect($channel->sends)->toBe(1)
        ->and($log->refresh()->status)->toBe(NotificationStatus::Sent);
});

it('marks the log failed and emits a metric when the job finally fails', function (): void {
    $log = pendingLog('mail');
    $recorder = new RecordingMetricsRecorder;
    app()->instance(MetricsRecorderInterface::class, $recorder);

    $job = new SendNotification(notificationMessage('mail'), $log->id);
    $job->failed(new RuntimeException('smtp unavailable'));

    expect($log->refresh()->status)->toBe(NotificationStatus::Failed)
        ->and($recorder->names())->toContain('notification.failed');

    $failure = collect($recorder->calls)->firstWhere('name', 'notification.failed');
    expect($failure['tags'])->toBe(['channel' => 'mail']);
});
