<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Content;

/**
 * The authority on which editorial state transitions are legal.
 *
 * Content models delegate transition validation here so every content type
 * shares one consistent lifecycle policy.
 *
 * @api This interface is part of the public extension API.
 */
interface WorkflowInterface
{
    /**
     * The states a piece of content may move to from the given state.
     *
     * @return array<int, WorkflowState>
     */
    public function allowedTransitions(WorkflowState $from): array;

    public function canTransition(WorkflowState $from, WorkflowState $to): bool;

    /**
     * @throws WorkflowExceptionInterface when the transition is not allowed.
     */
    public function assertCanTransition(WorkflowState $from, WorkflowState $to): void;
}
