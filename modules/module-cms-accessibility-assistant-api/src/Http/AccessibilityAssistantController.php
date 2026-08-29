<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistantApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\AccessibilityAssistant\Services\AccessibilityAssistantService;

final class AccessibilityAssistantController
{
    public function analyze(Request $request, AccessibilityAssistantService $service): JsonResponse
    {
        $data = $request->validate(['html' => ['required', 'string'], 'exceptions' => ['sometimes', 'array'], 'exceptions.*' => ['string']]);

        return response()->json(['data' => ['findings' => $service->analyze($data['html'], $data['exceptions'] ?? [])]]);
    }
}
