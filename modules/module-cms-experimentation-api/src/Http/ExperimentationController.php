<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperimentationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Experimentation\Queries\ExperimentationQuery;
use Liberu\Cms\Experimentation\Services\ExperimentationService;
use Liberu\Cms\ExperimentationApi\Http\Resources\ExperimentResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ExperimentationController
{
    public function index(Request $request, ExperimentationQuery $query): JsonResponse
    {
        $data = $request->validate(['search' => ['sometimes', 'nullable', 'string', 'max:255'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $experiments = $query->list((int) ($data['per_page'] ?? 15), (string) ($data['search'] ?? ''));

        return response()->json(['data' => ExperimentResource::collection($experiments->getCollection()), 'meta' => ['current_page' => $experiments->currentPage(), 'last_page' => $experiments->lastPage(), 'per_page' => $experiments->perPage(), 'total' => $experiments->total()]]);
    }

    public function show(string $key, ExperimentationQuery $query): ExperimentResource
    {
        $experiment = $query->find($key);
        if (! $experiment) {
            throw new NotFoundHttpException;
        }

        return new ExperimentResource($experiment);
    }

    public function store(Request $request, ExperimentationService $service): ExperimentResource
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:255'], 'name' => ['required', 'string', 'max:255'], 'type' => ['sometimes', 'in:ab,multivariate'], 'variants' => ['required', 'array', 'min:2'], 'variants.*.key' => ['required', 'string'], 'variants.*.name' => ['nullable', 'string'], 'variants.*.weight' => ['required', 'integer', 'min:1'], 'variants.*.content' => ['sometimes', 'array'], 'allocation_percentage' => ['sometimes', 'integer', 'between:1,100'], 'goals' => ['sometimes', 'array'], 'guardrails' => ['sometimes', 'array'], 'analysis_policy' => ['sometimes', 'array']]);

        return new ExperimentResource($service->create($data, $request->user()?->current_team_id));
    }

    public function start(string $key, ExperimentationQuery $query, ExperimentationService $service): ExperimentResource
    {
        $experiment = $query->find($key);
        if (! $experiment) {
            throw new NotFoundHttpException;
        }

        return new ExperimentResource($service->start($experiment));
    }

    public function allocate(Request $request, string $key, ExperimentationQuery $query, ExperimentationService $service): JsonResponse
    {
        $experiment = $query->active($key);
        if (! $experiment) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['subject_key' => ['required', 'string', 'max:255']]);
        $variant = $service->allocate($experiment, $data['subject_key']);

        return response()->json(['data' => $variant ? ['experiment_key' => $key, 'variant_key' => $variant->key, 'content' => $variant->content] : null]);
    }

    public function promote(Request $request, string $key, ExperimentationQuery $query, ExperimentationService $service): ExperimentResource
    {
        $experiment = $query->find($key);
        if (! $experiment) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['variant_key' => ['required', 'string'], 'reason' => ['nullable', 'string', 'max:2000']]);
        $variant = $experiment->variants->firstWhere('key', $data['variant_key']);
        if (! $variant) {
            throw new NotFoundHttpException;
        }

        return new ExperimentResource($service->promote($experiment, $variant, $data['reason'] ?? null, 'user', $request->user()?->getAuthIdentifier()));
    }
}
