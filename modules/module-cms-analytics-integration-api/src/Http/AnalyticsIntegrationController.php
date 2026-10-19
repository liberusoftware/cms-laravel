<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegrationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\AnalyticsIntegration\Services\AnalyticsIntegrationService;
use Liberu\Cms\AnalyticsIntegrationApi\Http\Resources\AnalyticsEventResource;

final class AnalyticsIntegrationController
{
    public function record(Request $request, AnalyticsIntegrationService $service): JsonResponse
    {
        $raw = $request->validate(['event_type' => ['required', 'string'], 'event_name' => ['required', 'string', 'max:120'], 'idempotency_key' => ['required', 'string', 'max:255'], 'subject_type' => ['sometimes', 'string', 'max:120'], 'subject_id' => ['sometimes', 'string', 'max:120'], 'consent_category' => ['sometimes', 'string', 'max:80'], 'consent_granted' => ['sometimes', 'boolean'], 'payload' => ['sometimes', 'array'], 'occurred_at' => ['sometimes', 'date']]);
        $data = $this->normalized($raw);

        return new AnalyticsEventResource($service->recordEvent($request->user()?->current_team_id, $data))->response()->setStatusCode(201);
    }

    public function mapping(Request $request, AnalyticsIntegrationService $service): JsonResponse
    {
        $raw = $request->validate(['event_type' => ['required', 'string'], 'provider' => ['required', 'string', 'max:100'], 'measurement_key' => ['required', 'string', 'max:255'], 'consent_category' => ['sometimes', 'string', 'max:80'], 'config' => ['sometimes', 'array'], 'enabled' => ['sometimes', 'boolean']]);
        $data = $this->normalized($raw);

        return response()->json(['data' => $service->saveMapping($request->user()?->current_team_id, $data)], 201);
    }

    public function dashboard(Request $request, AnalyticsIntegrationService $service): JsonResponse
    {
        return response()->json(['data' => $service->dashboard($request->user()?->current_team_id, $request->string('from')->toString() ?: null, $request->string('to')->toString() ?: null)]);
    }

    /** @return array<string, mixed> */
    private function normalized(mixed $value): array
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
