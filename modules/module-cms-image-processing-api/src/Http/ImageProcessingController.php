<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessingApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ImageProcessing\Queries\ImageProcessingQuery;
use Liberu\Cms\ImageProcessing\Services\ImageProcessingService;
use Liberu\Cms\ImageProcessingApi\Http\Resources\ImageDerivativeResource;
use Liberu\Cms\ImageProcessingApi\Http\Resources\ProcessingProfileResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ImageProcessingController
{
    public function profiles(Request $request, ImageProcessingQuery $query): JsonResponse
    {
        $raw = $request->validate(['search' => ['sometimes', 'nullable', 'string', 'max:120'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $data = is_array($raw) ? $raw : [];
        $profiles = $query->profiles(is_int($data['per_page'] ?? null) ? $data['per_page'] : 15, is_string($data['search'] ?? null) ? $data['search'] : '');

        return response()->json(['data' => ProcessingProfileResource::collection($profiles->getCollection()), 'meta' => ['current_page' => $profiles->currentPage(), 'last_page' => $profiles->lastPage(), 'per_page' => $profiles->perPage(), 'total' => $profiles->total()]]);
    }

    public function storeProfile(Request $request, ImageProcessingService $service): ProcessingProfileResource
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:120'], 'format' => ['sometimes', 'string'], 'quality' => ['sometimes', 'integer', 'min:1', 'max:100'], 'width' => ['nullable', 'integer', 'min:1', 'max:10000'], 'height' => ['nullable', 'integer', 'min:1', 'max:10000'], 'fit' => ['sometimes', 'in:cover,contain,crop,inside']]);
        if (! is_array($data) || ! is_string($data['key'] ?? null)) {
            throw ValidationException::withMessages(['key' => 'The profile payload is invalid.']);
        }

        return new ProcessingProfileResource($service->profile($data['key'], is_string($data['format'] ?? null) ? $data['format'] : 'webp', is_int($data['quality'] ?? null) ? $data['quality'] : 82, is_int($data['width'] ?? null) ? $data['width'] : null, is_int($data['height'] ?? null) ? $data['height'] : null, is_string($data['fit'] ?? null) ? $data['fit'] : 'cover', $request->user()?->current_team_id));
    }

    public function storeDerivative(string $key, Request $request, ImageProcessingQuery $query, ImageProcessingService $service): ImageDerivativeResource
    {
        $profile = $query->profile($key, $request->user()?->current_team_id);
        if (! $profile) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['asset_key' => ['required', 'string', 'max:500'], 'checksum' => ['required', 'string', 'max:128'], 'metadata' => ['sometimes', 'array']]);
        if (! is_array($data) || ! is_string($data['asset_key'] ?? null) || ! is_string($data['checksum'] ?? null)) {
            throw ValidationException::withMessages(['asset_key' => 'The derivative payload is invalid.']);
        }
        $metadata = [];
        if (is_array($data['metadata'] ?? null)) {
            foreach ($data['metadata'] as $name => $value) {
                if (is_string($name)) {
                    $metadata[$name] = $value;
                }
            }
        }

        return new ImageDerivativeResource($service->derivative($profile, $data['asset_key'], $data['checksum'], $metadata, $request->user()?->current_team_id));
    }
}
