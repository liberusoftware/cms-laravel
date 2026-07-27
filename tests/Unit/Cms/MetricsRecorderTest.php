<?php

declare(strict_types=1);

use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Contracts\Events\Content\ContentPublished;
use Liberu\Cms\Contracts\Events\Content\ContentStateChanged;
use Liberu\Cms\Contracts\Events\Form\FormSubmitted;
use Liberu\Cms\Contracts\Events\Media\MediaUploaded;
use Liberu\Cms\Contracts\Media\MediaItemInterface;
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;
use Liberu\Cms\Observability\Metrics\LogMetricsRecorder;
use Liberu\Cms\Observability\Metrics\MetricsSubscriber;
use Liberu\Cms\Observability\Metrics\NullMetricsRecorder;
use Psr\Log\AbstractLogger;

/**
 * A PSR logger that captures each record as {message, context}.
 */
function logSpy(): AbstractLogger
{
    return new class extends AbstractLogger
    {
        /** @var array<int, array{message: string, context: array<string, mixed>}> */
        public array $records = [];

        public function log($level, $message, array $context = []): void
        {
            $this->records[] = ['message' => (string) $message, 'context' => $context];
        }
    };
}

/**
 * A recorder that captures each increment call as [name, by, tags].
 */
function spyRecorder(): MetricsRecorderInterface
{
    return new class implements MetricsRecorderInterface
    {
        /** @var array<int, array{0: string, 1: int, 2: array<string, scalar>}> */
        public array $increments = [];

        public function increment(string $name, int $by = 1, array $tags = []): void
        {
            $this->increments[] = [$name, $by, $tags];
        }

        public function timing(string $name, float $milliseconds, array $tags = []): void {}

        public function gauge(string $name, float $value, array $tags = []): void {}
    };
}

it('writes one structured record per metric to the log', function (): void {
    $logger = logSpy();
    $recorder = new LogMetricsRecorder($logger);

    $recorder->increment('content.published');
    $recorder->timing('api.request', 12.5, ['route' => 'search']);
    $recorder->gauge('queue.depth', 3.0);

    expect($logger->records)->toHaveCount(3)
        ->and($logger->records[0]['context'])->toBe(['type' => 'counter', 'metric' => 'content.published', 'value' => 1, 'tags' => []])
        ->and($logger->records[1]['context'])->toBe(['type' => 'timing', 'metric' => 'api.request', 'value' => 12.5, 'tags' => ['route' => 'search']])
        ->and($logger->records[2]['context'])->toBe(['type' => 'gauge', 'metric' => 'queue.depth', 'value' => 3.0, 'tags' => []]);
});

it('honours the contract as a safe no-op when disabled', function (): void {
    $recorder = new NullMetricsRecorder;

    $recorder->increment('content.published');
    $recorder->timing('api.request', 1.0);
    $recorder->gauge('queue.depth', 1.0);
})->throwsNoExceptions();

it('maps each domain event to its counter', function (): void {
    $recorder = spyRecorder();
    $subscriber = new MetricsSubscriber($recorder);

    $subscriber->handleContentPublished(new ContentPublished('page', 1));
    $subscriber->handleContentStateChanged(new ContentStateChanged('page', 1, WorkflowState::Draft, WorkflowState::Published));
    $subscriber->handleFormSubmitted(new FormSubmitted('contact', 1, null, []));
    $subscriber->handleMediaUploaded(new MediaUploaded(Mockery::mock(MediaItemInterface::class)));

    expect(array_column($recorder->increments, 0))->toBe([
        'content.published',
        'content.state_changed',
        'form.submitted',
        'media.uploaded',
    ])->and($recorder->increments[1][2])->toBe(['from' => 'draft', 'to' => 'published']);
});
