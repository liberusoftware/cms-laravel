<?php

declare(strict_types=1);

namespace Liberu\Cms\Content\Workflow;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Liberu\Cms\Contracts\Content\WorkflowInterface;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Contracts\Events\Content\ContentPublished;
use Liberu\Cms\Contracts\Events\Content\ContentStateChanged;
use Liberu\Cms\Contracts\Events\EventBusInterface;

/**
 * Gives a content model the shared editorial lifecycle: workflow state, the
 * publish/review/archive/schedule transitions, and the events they emit.
 *
 * The model needs `status` and `published_at` columns; this trait casts them.
 * Transitions are validated by the Workflow service and broadcast on the bus.
 *
 * @mixin Model
 */
trait HasWorkflow
{
    public function initializeHasWorkflow(): void
    {
        $this->mergeCasts([
            'status' => WorkflowState::class,
            'published_at' => 'datetime',
        ]);
    }

    public function workflowState(): WorkflowState
    {
        $status = $this->getAttribute('status');

        return $status instanceof WorkflowState ? $status : WorkflowState::Draft;
    }

    public function isPublished(): bool
    {
        return $this->workflowState() === WorkflowState::Published;
    }

    public function isLive(): bool
    {
        if (! $this->isPublished()) {
            return false;
        }

        $publishedAt = $this->publishedAt();

        return $publishedAt === null || $publishedAt <= now();
    }

    public function publishedAt(): ?DateTimeInterface
    {
        $value = $this->getAttribute('published_at');

        return $value instanceof DateTimeInterface ? $value : null;
    }

    public function transitionTo(WorkflowState $to, ?DateTimeInterface $publishAt = null): void
    {
        $from = $this->workflowState();

        app(WorkflowInterface::class)->assertCanTransition($from, $to);

        $this->setAttribute('status', $to);

        if ($to === WorkflowState::Published) {
            $this->setAttribute('published_at', $publishAt ?? now());
        }

        $this->save();

        if ($from === $to) {
            return;
        }

        $events = app(EventBusInterface::class);
        $events->dispatch(new ContentStateChanged($this->contentType(), $this->contentId(), $from, $to));

        if ($to === WorkflowState::Published) {
            $events->dispatch(new ContentPublished($this->contentType(), $this->contentId()));
        }
    }

    public function submitForReview(): void
    {
        $this->transitionTo(WorkflowState::Review);
    }

    public function publish(): void
    {
        $this->transitionTo(WorkflowState::Published);
    }

    public function schedule(DateTimeInterface $when): void
    {
        $this->transitionTo(WorkflowState::Published, $when);
    }

    public function archive(): void
    {
        $this->transitionTo(WorkflowState::Archived);
    }

    public function returnToDraft(): void
    {
        $this->transitionTo(WorkflowState::Draft);
    }

    /**
     * The content type name used in events, e.g. "page", "post".
     */
    public function contentType(): string
    {
        return Str::kebab(class_basename($this));
    }

    private function contentId(): int|string
    {
        $key = $this->getKey();

        return is_int($key) || is_string($key) ? $key : (string) (is_scalar($key) ? $key : '');
    }
}
