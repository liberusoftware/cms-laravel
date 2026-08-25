<?php

declare(strict_types=1);

namespace Liberu\Cms\PersonalizationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Personalization\Models\Audience;
use Liberu\Cms\Personalization\Services\DecisionEngine;
use Liberu\Cms\Personalization\Services\PersonalizationService;

final class AudienceController
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['per_page' => ['sometimes', 'integer', 'min:1', 'max:100'], 'active' => ['sometimes', 'boolean']]);
        $audiences = Audience::query()->when(array_key_exists('active', $data), fn ($query) => $query->where('active', $data['active']))->withCount('variants')->latest()->paginate((int) ($data['per_page'] ?? 25));

        return response()->json(['data' => $audiences->through(fn (Audience $audience): array => $this->audienceData($audience, false)), 'meta' => ['current_page' => $audiences->currentPage(), 'last_page' => $audiences->lastPage(), 'per_page' => $audiences->perPage(), 'total' => $audiences->total()]]);
    }

    public function create(Request $request, PersonalizationService $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'key' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9-]*$/', 'max:120'], 'rules' => ['sometimes', 'array'], 'requires_consent' => ['sometimes', 'boolean'], 'active' => ['sometimes', 'boolean']]);

        return response()->json(['data' => $this->audienceData($service->createAudience($data, $request->user()?->current_team_id))], 201);
    }

    public function update(Request $request, string $key, PersonalizationService $service): JsonResponse
    {
        $audience = $this->managedAudience($key);
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:255'], 'key' => ['sometimes', 'string', 'regex:/^[a-z0-9][a-z0-9-]*$/', 'max:120'], 'rules' => ['sometimes', 'array'], 'requires_consent' => ['sometimes', 'boolean'], 'active' => ['sometimes', 'boolean']]);

        return response()->json(['data' => $this->audienceData($service->updateAudience($audience, $data))]);
    }

    public function destroy(string $key): JsonResponse
    {
        $this->managedAudience($key)->delete();

        return response()->json(status: 204);
    }

    public function createVariant(Request $request, string $key, PersonalizationService $service): JsonResponse
    {
        $audience = $this->managedAudience($key);
        $data = $request->validate(['key' => ['required', 'string', 'max:120'], 'payload' => ['required', 'array'], 'priority' => ['sometimes', 'integer', 'min:0'], 'holdout_percent' => ['sometimes', 'integer', 'min:0', 'max:100'], 'fallback' => ['sometimes', 'boolean'], 'active' => ['sometimes', 'boolean']]);

        return response()->json(['data' => $service->saveVariant($audience, $data)], 201);
    }

    public function show(string $key): JsonResponse
    {
        $audience = Audience::query()->where('key', $key)->where('active', true)->firstOrFail();

        return response()->json(['data' => $this->audienceData($audience)]);
    }

    public function decide(Request $request, string $key): JsonResponse
    {
        $data = $request->validate([
            'context' => ['sometimes', 'array'],
            'subject_key' => ['sometimes', 'nullable', 'string', 'max:255'],
            'consent' => ['sometimes', 'boolean'],
        ]);
        $decision = app(DecisionEngine::class)->decide(
            $key,
            $data['context'] ?? [],
            $data['subject_key'] ?? null,
            (bool) ($data['consent'] ?? false),
        );

        return response()->json(['data' => [
            'audience_key' => $key,
            'variant_key' => $decision['variant']?->key,
            'payload' => $decision['variant']?->payload,
            'reason' => $decision['reason'],
        ]]);
    }

    private function managedAudience(string $key): Audience
    {
        return Audience::query()->where('key', $key)->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function audienceData(Audience $audience, bool $includeVariants = true): array
    {
        $data = ['id' => $audience->getKey(), 'key' => $audience->key, 'name' => $audience->name, 'rules' => $audience->rules, 'requires_consent' => $audience->requires_consent, 'active' => $audience->active];
        if ($includeVariants) {
            $data['variants'] = $audience->variants()->where('active', true)->orderByDesc('priority')->get()->map(fn ($variant): array => ['id' => $variant->getKey(), 'key' => $variant->key, 'payload' => $variant->payload, 'priority' => $variant->priority, 'fallback' => $variant->fallback, 'holdout_percent' => $variant->holdout_percent])->all();
        }

        return $data;
    }
}
