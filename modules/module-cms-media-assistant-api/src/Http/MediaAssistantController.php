<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistantApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\MediaAssistant\Queries\MediaSuggestionQuery;
use Liberu\Cms\MediaAssistant\Services\MediaAssistantService;
use Liberu\Cms\MediaAssistantApi\Http\Resources\MediaSuggestionResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class MediaAssistantController
{
    public function index(Request $request, MediaSuggestionQuery $query): JsonResponse
    {
        $raw = $request->validate(['asset_key' => ['sometimes', 'nullable', 'string', 'max:500'], 'status' => ['sometimes', 'nullable', 'in:pending,accepted,rejected'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $data = is_array($raw) ? $raw : [];
        $assetKey = is_string($data['asset_key'] ?? null) ? $data['asset_key'] : '';
        $status = is_string($data['status'] ?? null) ? $data['status'] : '';
        $perPage = is_int($data['per_page'] ?? null) ? $data['per_page'] : 15;
        $suggestions = $query->paginate($perPage, $assetKey, $status);

        return response()->json(['data' => MediaSuggestionResource::collection($suggestions->getCollection()), 'meta' => ['current_page' => $suggestions->currentPage(), 'last_page' => $suggestions->lastPage(), 'per_page' => $suggestions->perPage(), 'total' => $suggestions->total()]]);
    }

    public function store(Request $request, MediaAssistantService $service): MediaSuggestionResource
    {
        $raw = $request->validate(['asset_key' => ['required', 'string', 'max:500'], 'kind' => ['required', 'in:alt_text,caption,transcript,tag,crop,rights_warning'], 'value' => ['required', 'string'], 'provider' => ['required', 'string', 'max:120'], 'model' => ['nullable', 'string', 'max:120'], 'confidence' => ['nullable', 'numeric', 'between:0,1'], 'provenance' => ['sometimes', 'array']]);
        if (! is_array($raw) || ! is_string($raw['asset_key'] ?? null) || ! is_string($raw['kind'] ?? null) || ! is_string($raw['value'] ?? null) || ! is_string($raw['provider'] ?? null)) {
            throw ValidationException::withMessages(['value' => 'The suggestion payload is invalid.']);
        }

        $provenance = [];
        if (is_array($raw['provenance'] ?? null)) {
            foreach ($raw['provenance'] as $key => $value) {
                if (is_string($key)) {
                    $provenance[$key] = $value;
                }
            }
        }

        return new MediaSuggestionResource($service->suggest($raw['asset_key'], $raw['kind'], $raw['value'], $raw['provider'], is_string($raw['model'] ?? null) ? $raw['model'] : null, is_numeric($raw['confidence'] ?? null) ? (float) $raw['confidence'] : null, $provenance, $request->user()?->current_team_id));
    }

    public function review(string $publicId, Request $request, MediaSuggestionQuery $query, MediaAssistantService $service): MediaSuggestionResource
    {
        $suggestion = $query->find($publicId, $request->user()?->current_team_id);
        if (! $suggestion) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['decision' => ['required', 'in:accepted,rejected'], 'reviewer_key' => ['required', 'string', 'max:255'], 'note' => ['nullable', 'string']]);
        if (! is_array($data) || ! is_string($data['decision'] ?? null) || ! is_string($data['reviewer_key'] ?? null)) {
            throw ValidationException::withMessages(['decision' => 'The review payload is invalid.']);
        }

        return new MediaSuggestionResource($service->review($suggestion, $data['decision'], $data['reviewer_key'], is_string($data['note'] ?? null) ? $data['note'] : null, $request->user()?->current_team_id));
    }
}
