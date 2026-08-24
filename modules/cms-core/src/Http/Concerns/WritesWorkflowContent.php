<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Http\Concerns;

use Liberu\Cms\Contracts\Content\WorkflowInterface;
use Liberu\Cms\Contracts\Content\WorkflowState;

/**
 * Shared status handling for Delivery API write controllers. A `status` field is
 * never written directly: it is pulled out of the payload and applied through
 * the editorial workflow, and an illegal transition is rejected with a 422.
 * Keeps the write controllers consistent without each re-implementing the rule.
 */
trait WritesWorkflowContent
{
    /**
     * Remove and resolve the requested workflow status from the payload.
     *
     * @param  array<string, mixed>  $data
     */
    protected function pullStatus(array &$data): ?WorkflowState
    {
        $value = $data['status'] ?? null;
        unset($data['status']);

        return is_string($value) ? WorkflowState::from($value) : null;
    }

    /**
     * Whether a transition to `$to` should run. Aborts with 422 when the
     * transition is not allowed; returns false when there is nothing to do.
     */
    protected function shouldTransition(WorkflowState $from, ?WorkflowState $to): bool
    {
        if (! $to instanceof WorkflowState || $to === $from) {
            return false;
        }

        if (! app(WorkflowInterface::class)->canTransition($from, $to)) {
            abort(422, 'Illegal status transition.');
        }

        return true;
    }
}
