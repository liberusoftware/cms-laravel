<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflowApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\EditorialWorkflow\Models\EditorialWorkflow;
use Liberu\Cms\EditorialWorkflow\Queries\EditorialWorkflowQuery;
use Liberu\Cms\EditorialWorkflow\Services\EditorialWorkflowService;
use Liberu\Cms\EditorialWorkflowApi\Http\Resources\EditorialWorkflowResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EditorialWorkflowController
{
    public function index(Request $request, EditorialWorkflowQuery $query): JsonResponse
    {
        $data = $request->validate(['search' => ['sometimes', 'nullable', 'string', 'max:255'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $data = is_array($data) ? $data : [];
        $workflows = $query->paginate(is_int($data['per_page'] ?? null) ? $data['per_page'] : 15, is_string($data['search'] ?? null) ? $data['search'] : '');

        return response()->json(['data' => EditorialWorkflowResource::collection($workflows->getCollection()), 'meta' => ['current_page' => $workflows->currentPage(), 'last_page' => $workflows->lastPage(), 'per_page' => $workflows->perPage(), 'total' => $workflows->total()]]);
    }

    public function store(Request $request, EditorialWorkflowService $service): EditorialWorkflowResource
    {
        $raw = $request->validate(['key' => ['required', 'string', 'max:120'], 'name' => ['required', 'string', 'max:200'], 'initial_state' => ['sometimes', 'string', 'max:120']]);
        $data = [];
        if (is_array($raw)) {
            foreach ($raw as $key => $value) {
                if (is_string($key)) {
                    $data[$key] = $value;
                }
            }
        }

        if (! is_string($data['key'] ?? null) || ! is_string($data['name'] ?? null)) {
            throw ValidationException::withMessages(['key' => 'The workflow payload is invalid.']);
        }

        return new EditorialWorkflowResource($service->create($data, $request->user()?->current_team_id));
    }

    public function show(string $publicId, Request $request, EditorialWorkflowQuery $query): EditorialWorkflowResource
    {
        return new EditorialWorkflowResource($this->workflow($publicId, $request, $query));
    }

    public function state(string $publicId, Request $request, EditorialWorkflowQuery $query, EditorialWorkflowService $service): EditorialWorkflowResource
    {
        $data = $request->validate(['key' => ['required', 'string'], 'label' => ['required', 'string'], 'terminal' => ['sometimes', 'boolean']]);
        $workflow = $this->workflow($publicId, $request, $query);
        if (! is_array($data) || ! is_string($data['key'] ?? null) || ! is_string($data['label'] ?? null)) {
            throw ValidationException::withMessages(['key' => 'The state payload is invalid.']);
        }
        $service->state($workflow, $data['key'], $data['label'], is_bool($data['terminal'] ?? null) ? $data['terminal'] : false);

        return new EditorialWorkflowResource($workflow->refresh());
    }

    public function transition(string $publicId, Request $request, EditorialWorkflowQuery $query, EditorialWorkflowService $service): EditorialWorkflowResource
    {
        $data = $request->validate(['from_state' => ['required', 'string'], 'to_state' => ['required', 'string'], 'permission' => ['nullable', 'string'], 'requires_review' => ['sometimes', 'boolean']]);
        $workflow = $this->workflow($publicId, $request, $query);
        if (! is_array($data) || ! is_string($data['from_state'] ?? null) || ! is_string($data['to_state'] ?? null)) {
            throw ValidationException::withMessages(['from_state' => 'The transition payload is invalid.']);
        }
        $service->transition($workflow, $data['from_state'], $data['to_state'], is_string($data['permission'] ?? null) ? $data['permission'] : null, is_bool($data['requires_review'] ?? null) ? $data['requires_review'] : false);

        return new EditorialWorkflowResource($workflow->refresh());
    }

    public function assign(string $publicId, Request $request, EditorialWorkflowQuery $query, EditorialWorkflowService $service): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'actor_type' => ['required', 'string'], 'actor_key' => ['required', 'string'], 'role' => ['sometimes', 'string'], 'delegated_from' => ['nullable', 'integer']]);
        $workflow = $this->workflow($publicId, $request, $query);
        if (! is_array($data) || ! is_string($data['subject_type'] ?? null) || ! is_string($data['subject_key'] ?? null) || ! is_string($data['actor_type'] ?? null) || ! is_string($data['actor_key'] ?? null)) {
            throw ValidationException::withMessages(['subject_key' => 'The assignment payload is invalid.']);
        }

        return response()->json(['data' => $service->assign($workflow, $data['subject_type'], $data['subject_key'], $data['actor_type'], $data['actor_key'], is_string($data['role'] ?? null) ? $data['role'] : 'assignee', is_int($data['delegated_from'] ?? null) ? $data['delegated_from'] : null)], 201);
    }

    public function review(string $publicId, Request $request, EditorialWorkflowQuery $query, EditorialWorkflowService $service): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'reviewer_key' => ['required', 'string'], 'decision' => ['required', 'in:approved,rejected,changes_requested'], 'comment' => ['nullable', 'string']]);
        $workflow = $this->workflow($publicId, $request, $query);
        if (! is_array($data) || ! is_string($data['subject_type'] ?? null) || ! is_string($data['subject_key'] ?? null) || ! is_string($data['reviewer_key'] ?? null) || ! is_string($data['decision'] ?? null)) {
            throw ValidationException::withMessages(['subject_key' => 'The review payload is invalid.']);
        }

        return response()->json(['data' => $service->review($workflow, $data['subject_type'], $data['subject_key'], $data['reviewer_key'], $data['decision'], is_string($data['comment'] ?? null) ? $data['comment'] : null)], 201);
    }

    public function evidence(string $publicId, Request $request, EditorialWorkflowQuery $query, EditorialWorkflowService $service): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'event' => ['required', 'string'], 'actor_key' => ['nullable', 'string'], 'payload' => ['sometimes', 'array']]);
        $workflow = $this->workflow($publicId, $request, $query);
        if (! is_array($data) || ! is_string($data['subject_type'] ?? null) || ! is_string($data['subject_key'] ?? null) || ! is_string($data['event'] ?? null)) {
            throw ValidationException::withMessages(['subject_key' => 'The evidence payload is invalid.']);
        }

        $payload = is_array($data['payload'] ?? null) ? $data['payload'] : [];

        /** @var array<string, mixed> $payload */
        return response()->json(['data' => $service->evidence($workflow, $data['subject_type'], $data['subject_key'], $data['event'], is_string($data['actor_key'] ?? null) ? $data['actor_key'] : null, $payload)], 201);
    }

    private function workflow(string $publicId, Request $request, EditorialWorkflowQuery $query): EditorialWorkflow
    {
        $workflow = $query->find($publicId, $request->user()?->current_team_id);
        if (! $workflow) {
            throw new NotFoundHttpException;
        }

        return $workflow;
    }
}
