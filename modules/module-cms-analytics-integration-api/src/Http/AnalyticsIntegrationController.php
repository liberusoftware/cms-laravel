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
        $data = $request->validate(['event_type' => ['required', 'string'], 'event_name' => ['required', 'string', 'max:120'], 'idempotency_key' => ['required', 'string', 'max:255'], 'subject_type' => ['sometimes', 'string', 'max:120'], 'subject_id' => ['sometimes', 'string', 'max:120'], 'consent_category' => ['sometimes', 'string', 'max:80'], 'consent_granted' => ['sometimes', 'boolean'], 'payload' => ['sometimes', 'array'], 'occurred_at' => ['sometimes', 'date']]);

        return (new AnalyticsEventResource($service->recordEvent(null, $data)))->response()->setStatusCode(201);
    }

    public function mapping(Request $request, AnalyticsIntegrationService $service): JsonResponse
    {
        $data = $request->validate(['event_type' => ['required', 'string'], 'provider' => ['required', 'string', 'max:100'], 'measurement_key' => ['required', 'string', 'max:255'], 'consent_category' => ['sometimes', 'string', 'max:80'], 'config' => ['sometimes', 'array'], 'enabled' => ['sometimes', 'boolean']]);

        return response()->json(['data' => $service->saveMapping(null, $data)], 201);
    }

    public function dashboard(Request $request, AnalyticsIntegrationService $service): JsonResponse
    {
        return response()->json(['data' => $service->dashboard(null, $request->string('from')->toString() ?: null, $request->string('to')->toString() ?: null)]);
    }
}
