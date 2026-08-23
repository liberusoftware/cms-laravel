<?php

declare(strict_types=1);

namespace Liberu\Cms\Content\Workflow;

use Liberu\Cms\Contracts\Content\WorkflowInterface;
use Liberu\Cms\Contracts\Content\WorkflowState;

/**
 * The shared editorial transition policy.
 *
 *   Draft    → Review, Published, Archived
 *   Review   → Draft, Published, Archived
 *   Published→ Draft (unpublish), Archived
 *   Archived → Draft (restore)
 *
 * Staying in the same state is always allowed (idempotent saves).
 */
final class Workflow implements WorkflowInterface
{
    public function allowedTransitions(WorkflowState $from): array
    {
        return match ($from) {
            WorkflowState::Draft => [WorkflowState::Review, WorkflowState::Published, WorkflowState::Archived],
            WorkflowState::Review => [WorkflowState::Draft, WorkflowState::Published, WorkflowState::Archived],
            WorkflowState::Published => [WorkflowState::Draft, WorkflowState::Archived],
            WorkflowState::Archived => [WorkflowState::Draft],
        };
    }

    public function canTransition(WorkflowState $from, WorkflowState $to): bool
    {
        return $from === $to || in_array($to, $this->allowedTransitions($from), true);
    }

    public function assertCanTransition(WorkflowState $from, WorkflowState $to): void
    {
        if (! $this->canTransition($from, $to)) {
            throw InvalidWorkflowTransition::between($from, $to);
        }
    }
}
