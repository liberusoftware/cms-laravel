<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegrationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ForumsIntegration\Queries\ForumReferenceQuery;
use Liberu\Cms\ForumsIntegration\Services\ForumsIntegrationService;
use Liberu\Cms\ForumsIntegrationApi\Http\Resources\ForumReferenceResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ForumsIntegrationController
{
    public function index(Request $request, ForumReferenceQuery $query): JsonResponse
    {
        $data = $request->validate(['provider' => ['sometimes', 'nullable', 'string', 'max:180'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $data = is_array($data) ? $data : [];
        $references = $query->paginate(is_int($data['per_page'] ?? null) ? $data['per_page'] : 15, is_string($data['provider'] ?? null) ? $data['provider'] : '');

        return response()->json(['data' => ForumReferenceResource::collection($references->getCollection()), 'meta' => ['current_page' => $references->currentPage(), 'last_page' => $references->lastPage(), 'per_page' => $references->perPage(), 'total' => $references->total()]]);
    }

    public function link(Request $request, ForumsIntegrationService $service): ForumReferenceResource
    {
        $data = $request->validate(['provider' => ['required', 'string', 'max:180'], 'external_type' => ['required', 'string', 'max:180'], 'external_id' => ['required', 'string', 'max:180'], 'url' => ['nullable', 'url'], 'metadata' => ['sometimes', 'array']]);
        if (! is_array($data) || ! is_string($data['provider'] ?? null) || ! is_string($data['external_type'] ?? null) || ! is_string($data['external_id'] ?? null)) {
            throw ValidationException::withMessages(['provider' => 'The forum reference payload is invalid.']);
        }
        $metadata = [];
        if (is_array($data['metadata'] ?? null)) {
            foreach ($data['metadata'] as $key => $value) {
                if (is_string($key)) {
                    $metadata[$key] = $value;
                }
            }
        }

        return new ForumReferenceResource($service->link($data['provider'], $data['external_type'], $data['external_id'], is_string($data['url'] ?? null) ? $data['url'] : null, $request->user()?->current_team_id, $metadata));
    }

    public function recent(string $provider, Request $request, ForumsIntegrationService $service): JsonResponse
    {
        $data = $request->validate(['limit' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $limit = is_array($data) && is_int($data['limit'] ?? null) ? $data['limit'] : 10;

        return response()->json(['data' => $service->recent($provider, $request->user()?->current_team_id, $limit)]);
    }

    public function moderation(string $publicId, Request $request, ForumReferenceQuery $query, ForumsIntegrationService $service): JsonResponse
    {
        $reference = $query->find($publicId, $request->user()?->current_team_id);
        if (! $reference) {
            throw new NotFoundHttpException;
        }

        return response()->json(['data' => ['url' => $service->moderationUrl($reference, $request->user()?->current_team_id)]]);
    }
}
