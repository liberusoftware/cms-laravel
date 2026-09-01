<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperationsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\FormOperations\Queries\OperationalSubmissionQuery;
use Liberu\Cms\FormOperations\Services\FormOperationsService;
use Liberu\Cms\FormOperationsApi\Http\Resources\OperationalSubmissionResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class FormOperationsController
{
    public function index(Request $request, OperationalSubmissionQuery $query): JsonResponse
    {
        $data = $request->validate(['form_id' => ['sometimes', 'integer', 'min:1'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $data = is_array($data) ? $data : [];
        $submissions = $query->paginate(is_int($data['per_page'] ?? null) ? $data['per_page'] : 15, is_int($data['form_id'] ?? null) ? $data['form_id'] : null);

        return response()->json(['data' => OperationalSubmissionResource::collection($submissions->getCollection()), 'meta' => ['current_page' => $submissions->currentPage(), 'last_page' => $submissions->lastPage(), 'per_page' => $submissions->perPage(), 'total' => $submissions->total()]]);
    }

    public function submit(Request $request, FormOperationsService $service): OperationalSubmissionResource
    {
        $data = $request->validate(['form_id' => ['required', 'integer', 'min:1'], 'payload' => ['required', 'array'], 'consented' => ['accepted'], 'retention_days' => ['sometimes', 'integer', 'min:1'], 'max_per_minute' => ['sometimes', 'integer', 'min:1']]);
        if (! is_array($data) || ! is_int($data['form_id'] ?? null) || ! is_array($data['payload'] ?? null)) {
            throw ValidationException::withMessages(['submission' => 'The submission payload is invalid.']);
        }
        $payload = [];
        foreach ($data['payload'] as $key => $value) {
            if (is_string($key)) {
                $payload[$key] = $value;
            }
        }
        $fingerprint = (string) ($request->header('X-Client-Fingerprint') ?? $request->ip() ?? '');

        return new OperationalSubmissionResource($service->submit($data['form_id'], $payload, $fingerprint, true, $request->user()?->current_team_id, is_int($data['retention_days'] ?? null) ? $data['retention_days'] : 30, is_int($data['max_per_minute'] ?? null) ? $data['max_per_minute'] : 10));
    }

    public function export(string $publicId, Request $request, OperationalSubmissionQuery $query, FormOperationsService $service): JsonResponse
    {
        $submission = $query->find($publicId, $request->user()?->current_team_id);
        if (! $submission) {
            throw new NotFoundHttpException;
        }

        return response()->json(['data' => $service->export($submission, $request->user()?->current_team_id)]);
    }
}
