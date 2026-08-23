<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Content;

use DateTimeInterface;

/**
 * A content item that participates in the editorial workflow.
 *
 * Implemented by every content model (pages, posts, custom types) so consumers
 * can reason about publication state without knowing the concrete class.
 *
 * @api This interface is part of the public extension API.
 */
interface PublishableInterface
{
    public function workflowState(): WorkflowState;

    /**
     * Whether the item is in the Published state (regardless of schedule).
     */
    public function isPublished(): bool;

    /**
     * Whether the item is published and its publish date has arrived.
     */
    public function isLive(): bool;

    public function publishedAt(): ?DateTimeInterface;
}
