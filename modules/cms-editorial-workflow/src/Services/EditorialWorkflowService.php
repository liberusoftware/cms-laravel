<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflow\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\EditorialWorkflow\Models\EditorialWorkflow;
use Liberu\Cms\EditorialWorkflow\Models\WorkflowAssignment;
use Liberu\Cms\EditorialWorkflow\Models\WorkflowEvidence;
use Liberu\Cms\EditorialWorkflow\Models\WorkflowReview;

final class EditorialWorkflowService
{
    /** @param array<string, mixed> $data */
    public function create(array $data, ?int $teamId = null): EditorialWorkflow
    {
        $key = $this->required($data, 'key');
        $name = $this->required($data, 'name');
        $initial = $this->required($data, 'initial_state', 'draft');

        return DB::transaction(fn (): EditorialWorkflow => EditorialWorkflow::query()->create(['team_id' => $teamId, 'public_id' => (string) Str::uuid(), 'key' => $key, 'name' => $name, 'initial_state' => $initial]));
    }

    public function state(EditorialWorkflow $workflow, string $key, string $label, bool $terminal = false): void
    {
        $this->requiredString($key, 'key');
        $this->requiredString($label, 'label');
        $workflow->states()->updateOrCreate(['key' => $key], ['label' => $label, 'terminal' => $terminal]);
    }

    public function transition(EditorialWorkflow $workflow, string $from, string $to, ?string $permission = null, bool $requiresReview = false): void
    {
        $this->requiredString($from, 'from_state');
        $this->requiredString($to, 'to_state');
        if ($from === $to) {
            throw ValidationException::withMessages(['to_state' => 'A transition must change state.']);
        }
        $workflow->transitions()->updateOrCreate(['from_state' => $from, 'to_state' => $to], ['permission' => $permission, 'requires_review' => $requiresReview]);
    }

    public function assign(EditorialWorkflow $workflow, string $subjectType, string $subjectKey, string $actorType, string $actorKey, string $role = 'assignee', ?int $delegatedFrom = null): WorkflowAssignment
    {
        foreach (['subjectType' => $subjectType, 'subjectKey' => $subjectKey, 'actorType' => $actorType, 'actorKey' => $actorKey, 'role' => $role] as $field => $value) {
            $this->requiredString($value, $field);
        }

        return WorkflowAssignment::query()->updateOrCreate(['workflow_id' => $workflow->id, 'subject_type' => $subjectType, 'subject_key' => $subjectKey, 'actor_type' => $actorType, 'actor_key' => $actorKey, 'role' => $role], ['status' => 'active', 'delegated_from_id' => $delegatedFrom]);
    }

    public function review(EditorialWorkflow $workflow, string $subjectType, string $subjectKey, string $reviewerKey, string $decision, ?string $comment = null): WorkflowReview
    {
        foreach (['subjectType' => $subjectType, 'subjectKey' => $subjectKey, 'reviewerKey' => $reviewerKey, 'decision' => $decision] as $field => $value) {
            $this->requiredString($value, $field);
        }
        if (! in_array($decision, ['approved', 'rejected', 'changes_requested'], true)) {
            throw ValidationException::withMessages(['decision' => 'The review decision is invalid.']);
        }
        if ($workflow->assignments()->where(['subject_type' => $subjectType, 'subject_key' => $subjectKey, 'actor_key' => $reviewerKey, 'status' => 'active'])->exists()) {
            throw ValidationException::withMessages(['reviewer_key' => 'An assignee cannot review the same subject.']);
        }

        return WorkflowReview::query()->create(['workflow_id' => $workflow->id, 'subject_type' => $subjectType, 'subject_key' => $subjectKey, 'reviewer_key' => $reviewerKey, 'decision' => $decision, 'comment' => $comment]);
    }

    /** @param array<string, mixed> $payload */
    public function evidence(EditorialWorkflow $workflow, string $subjectType, string $subjectKey, string $event, ?string $actorKey = null, array $payload = []): WorkflowEvidence
    {
        $this->requiredString($subjectType, 'subject_type');
        $this->requiredString($subjectKey, 'subject_key');
        $this->requiredString($event, 'event');

        return WorkflowEvidence::query()->create(['workflow_id' => $workflow->id, 'subject_type' => $subjectType, 'subject_key' => $subjectKey, 'event' => $event, 'actor_key' => $actorKey, 'payload' => $payload]);
    }

    /** @param array<string, mixed> $data */
    private function required(array $data, string $field, string $default = ''): string
    {
        $value = $data[$field] ?? $default;

        return $this->requiredString($value, $field);
    }

    private function requiredString(mixed $value, string $field): string
    {
        if (! is_string($value) || trim($value) === '' || strlen($value) > 255) {
            throw ValidationException::withMessages([$field => 'A valid non-empty value is required.']);
        }

        return $value;
    }
}
