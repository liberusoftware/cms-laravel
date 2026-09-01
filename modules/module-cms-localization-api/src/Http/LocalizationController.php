<?php

declare(strict_types=1);

namespace Liberu\Cms\LocalizationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Localization\Queries\LocalizationQuery;
use Liberu\Cms\Localization\Services\LocalizationService;
use Liberu\Cms\LocalizationApi\Http\Resources\LocalizationResource;

final class LocalizationController
{
    public function locales(Request $request, LocalizationQuery $query): JsonResponse
    {
        $items = $query->locales($request->integer('per_page', 15), $request->user()?->current_team_id, $request->string('search')->toString());

        return response()->json(['data' => LocalizationResource::collection($items->getCollection()), 'meta' => ['current_page' => $items->currentPage(), 'last_page' => $items->lastPage(), 'total' => $items->total()]]);
    }

    public function variants(Request $request, LocalizationQuery $query): JsonResponse
    {
        $items = $query->variants($request->integer('per_page', 15), $request->user()?->current_team_id, $request->string('search')->toString());

        return response()->json(['data' => LocalizationResource::collection($items->getCollection()), 'meta' => ['current_page' => $items->currentPage(), 'last_page' => $items->lastPage(), 'total' => $items->total()]]);
    }

    public function storeLocale(Request $request, LocalizationService $service): LocalizationResource
    {
        $data = $this->validated($request->validate(['locale' => ['required', 'string', 'max:35'], 'fallback_locale' => ['nullable', 'string', 'max:35'], 'direction' => ['sometimes', 'in:ltr,rtl']]));

        return new LocalizationResource($service->locale($this->string($data, 'locale'), $request->user()?->current_team_id, $this->nullableString($data, 'fallback_locale'), $this->nullableString($data, 'direction') ?? 'ltr'));
    }

    public function variant(Request $request, LocalizationService $service): JsonResponse
    {
        $raw = $request->validate(['source_type' => 'required|string|max:120', 'source_key' => 'required|string|max:255', 'field' => 'required|string|max:120', 'locale' => 'required|string|max:35', 'value' => 'required|string', 'localized_slug' => 'nullable|string|max:240', 'status' => 'sometimes|in:draft,complete']);
        $data = [];
        if (is_array($raw)) {
            foreach ($raw as $key => $value) {
                if (is_string($key)) {
                    $data[$key] = $value;
                }
            }
        }

        return response()->json(['data' => $service->variant($this->string($data, 'source_type'), $this->string($data, 'source_key'), $this->string($data, 'field'), $this->string($data, 'locale'), $this->string($data, 'value'), $request->user()?->current_team_id, $this->nullableString($data, 'localized_slug'), $this->nullableString($data, 'status') ?? 'draft')], 201);
    }

    public function resolve(Request $request, LocalizationService $service): JsonResponse
    {
        $data = $this->validated($request->validate(['source_type' => 'required|string', 'source_key' => 'required|string', 'field' => 'required|string', 'locale' => 'required|string', 'fallback' => 'nullable|string']));
        $variant = $service->resolve($this->string($data, 'source_type'), $this->string($data, 'source_key'), $this->string($data, 'field'), $this->string($data, 'locale'), $request->user()?->current_team_id, $this->nullableString($data, 'fallback'));

        return response()->json(['data' => $variant ? (new LocalizationResource($variant))->resolve($request) : null]);
    }

    public function completeness(Request $request, LocalizationService $service): JsonResponse
    {
        $data = $this->validated($request->validate(['source_type' => 'required|string', 'source_key' => 'required|string', 'locale' => 'required|string']));

        return response()->json(['data' => ['completeness' => $service->completeness($this->string($data, 'source_type'), $this->string($data, 'source_key'), $this->string($data, 'locale'), $request->user()?->current_team_id)]]);
    }

    /** @param array<string, mixed> $data */
    private function string(array $data, string $key): string
    {
        $value = $data[$key] ?? null;
        abort_unless(is_string($value), 422);

        return $value;
    }

    /** @param array<string, mixed> $data */
    private function nullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;
        abort_unless($value === null || is_string($value), 422);

        return $value;
    }

    /** @return array<string, mixed> */
    private function validated(mixed $value): array
    {
        $data = [];
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if (is_string($key)) {
                    $data[$key] = $item;
                }
            }
        }

        return $data;
    }
}
