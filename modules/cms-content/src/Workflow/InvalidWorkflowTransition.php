<?php

declare(strict_types=1);

namespace Liberu\Cms\Content\Workflow;

use Liberu\Cms\Contracts\Content\WorkflowExceptionInterface;
use Liberu\Cms\Contracts\Content\WorkflowState;
use RuntimeException;

final class InvalidWorkflowTransition extends RuntimeException implements WorkflowExceptionInterface
{
    public static function between(WorkflowState $from, WorkflowState $to): self
    {
        return new self("Cannot transition content from [{$from->value}] to [{$to->value}].");
    }
}
