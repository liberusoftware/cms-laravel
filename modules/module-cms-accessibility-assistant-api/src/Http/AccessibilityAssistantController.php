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
        $raw = $request->validate(['html' => ['required', 'string'], 'exceptions' => ['sometimes', 'array'], 'exceptions.*' => ['string']]);
        $data = is_array($raw) ? $raw : [];
        $html = is_string($data['html'] ?? null) ? $data['html'] : '';
        $exceptions = [];
        if (is_array($data['exceptions'] ?? null)) {
            foreach ($data['exceptions'] as $exception) {
                if (is_string($exception)) {
                    $exceptions[] = $exception;
                }
            }
        }

        return response()->json(['data' => ['findings' => $service->analyze($html, $exceptions)]]);
    }
}
