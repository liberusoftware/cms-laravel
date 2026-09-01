<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflowApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\EditorialWorkflow\Models\EditorialWorkflow;
use LogicException;

final class EditorialWorkflowResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof EditorialWorkflow) {
            throw new LogicException('EditorialWorkflowResource requires an EditorialWorkflow instance.');
        }
        $workflow = $this->resource;

        return ['id' => $workflow->public_id, 'type' => 'cms-editorial-workflows', 'key' => $workflow->key, 'name' => $workflow->name, 'initial_state' => $workflow->initial_state, 'states' => $workflow->states?->map(fn ($state): array => ['key' => $state->key, 'label' => $state->label, 'terminal' => (bool) $state->terminal])->values(), 'transitions' => $workflow->transitions?->map(fn ($transition): array => ['from_state' => $transition->from_state, 'to_state' => $transition->to_state, 'permission' => $transition->permission, 'requires_review' => (bool) $transition->requires_review])->values(), 'created_at' => $workflow->created_at?->toISOString(), 'updated_at' => $workflow->updated_at?->toISOString()];
    }
}
