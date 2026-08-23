<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Events\Content;

use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Contracts\Events\CmsEvent;

/**
 * Emitted whenever a content item moves between editorial states. Carries only
 * identifiers and states, so listeners stay decoupled from the content model.
 *
 * @api This class is part of the public extension API.
 */
final readonly class ContentStateChanged implements CmsEvent
{
    public function __construct(
        public string $contentType,
        public int|string $contentId,
        public WorkflowState $from,
        public WorkflowState $to,
    ) {}

    public function name(): string
    {
        return 'content.state_changed';
    }
}
