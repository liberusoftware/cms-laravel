<?php

declare(strict_types=1);

namespace Liberu\Cms\PersonalizationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Personalization\Models\Audience;
use Liberu\Cms\Personalization\Services\DecisionEngine;

final class AudienceController
{
    public function show(string $key): JsonResponse
    {
        $audience = Audience::query()->where('key', $key)->where('active', true)->firstOrFail();

        return response()->json(['data' => ['key' => $audience->key, 'name' => $audience->name, 'requires_consent' => $audience->requires_consent]]);
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
}
