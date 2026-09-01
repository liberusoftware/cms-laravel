<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistantApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ExperienceAssistant\Queries\ExperienceSuggestionQuery;
use Liberu\Cms\ExperienceAssistant\Services\ExperienceAssistantService;
use Liberu\Cms\ExperienceAssistantApi\Http\Resources\ExperienceSuggestionResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ExperienceAssistantController
{
    public function index(Request $request, ExperienceSuggestionQuery $query): JsonResponse
    {
        $data = $request->validate(['surface' => ['sometimes', 'nullable', 'string', 'max:180'], 'status' => ['sometimes', 'nullable', 'in:pending,approved'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $data = is_array($data) ? $data : [];
        $suggestions = $query->paginate(is_int($data['per_page'] ?? null) ? $data['per_page'] : 15, is_string($data['surface'] ?? null) ? $data['surface'] : '', is_string($data['status'] ?? null) ? $data['status'] : '');

        return response()->json(['data' => ExperienceSuggestionResource::collection($suggestions->getCollection()), 'meta' => ['current_page' => $suggestions->currentPage(), 'last_page' => $suggestions->lastPage(), 'per_page' => $suggestions->perPage(), 'total' => $suggestions->total()]]);
    }

    public function store(Request $request, ExperienceAssistantService $service): ExperienceSuggestionResource
    {
        $data = $request->validate(['surface' => ['required', 'string', 'max:180'], 'definition' => ['required', 'array'], 'constraints' => ['sometimes', 'array']]);
        if (! is_array($data) || ! is_string($data['surface'] ?? null) || ! is_array($data['definition'] ?? null)) {
            throw ValidationException::withMessages(['definition' => 'The suggestion payload is invalid.']);
        }
        $constraints = is_array($data['constraints'] ?? null) ? $data['constraints'] : [];

        return new ExperienceSuggestionResource($service->suggest($data['surface'], $this->stringKeyedArray($data['definition']), $this->stringKeyedArray($constraints), $request->user()?->current_team_id));
    }

    public function check(Request $request, ExperienceAssistantService $service): JsonResponse
    {
        $data = $request->validate(['definition' => ['required', 'array'], 'constraints' => ['sometimes', 'array']]);
        if (! is_array($data) || ! is_array($data['definition'] ?? null)) {
            throw ValidationException::withMessages(['definition' => 'A block definition is required.']);
        }

        return response()->json(['data' => $service->check($this->stringKeyedArray($data['definition']), $this->stringKeyedArray($data['constraints'] ?? []))]);
    }

    public function approve(string $publicId, Request $request, ExperienceSuggestionQuery $query, ExperienceAssistantService $service): ExperienceSuggestionResource
    {
        $suggestion = $query->find($publicId, $request->user()?->current_team_id);
        if (! $suggestion) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['reviewer_key' => ['required', 'string', 'max:180']]);
        if (! is_array($data) || ! is_string($data['reviewer_key'] ?? null)) {
            throw ValidationException::withMessages(['reviewer_key' => 'A reviewer is required.']);
        }

        return new ExperienceSuggestionResource($service->approve($suggestion, $data['reviewer_key'], $request->user()?->current_team_id));
    }

    /** @return array<string, mixed> */
    private function stringKeyedArray(mixed $value): array
    {
        $result = [];
        if (! is_array($value)) {
            return $result;
        }
        foreach ($value as $key => $item) {
            if (is_string($key)) {
                $result[$key] = $item;
            }
        }

        return $result;
    }
}
