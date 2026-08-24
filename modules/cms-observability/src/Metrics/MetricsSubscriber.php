<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability\Metrics;

use Liberu\Cms\Contracts\Events\Content\ContentStateChanged;
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;

/**
 * Turns domain events into counter metrics with zero coupling: a pure EventBus
 * listener (like the audit and notifications subscribers) that imports no
 * emitter. Removing observability just stops the counters.
 */
final readonly class MetricsSubscriber
{
    public function __construct(private MetricsRecorderInterface $recorder) {}

    public function handleContentPublished(): void
    {
        $this->recorder->increment('content.published');
    }

    public function handleContentStateChanged(ContentStateChanged $event): void
    {
        $this->recorder->increment('content.state_changed', tags: [
            'from' => $event->from->value,
            'to' => $event->to->value,
        ]);
    }

    public function handleFormSubmitted(): void
    {
        $this->recorder->increment('form.submitted');
    }

    public function handleMediaUploaded(): void
    {
        $this->recorder->increment('media.uploaded');
    }
}
