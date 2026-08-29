<?php

declare(strict_types=1);

namespace Liberu\Cms\CopilotApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Copilot\Models\CopilotRequest;
use Liberu\Cms\Copilot\Services\CopilotService;

final class CopilotController
{
    public function submit(Request $request, CopilotService $service): JsonResponse
    {
        $data = $request->validate(['capability' => ['required', 'string'], 'prompt' => ['required', 'string', 'max:10000'], 'input' => ['sometimes', 'array'], 'idempotency_key' => ['sometimes', 'string', 'max:255']]);

        return response()->json(['data' => $service->submit(null, $data['capability'], $data['prompt'], $data['input'] ?? [], $data['idempotency_key'] ?? null)], 201);
    }

    public function execute(CopilotRequest $request, CopilotService $service): JsonResponse
    {
        return response()->json(['data' => $service->execute($request)]);
    }

    public function confirm(Request $request, CopilotRequest $copilotRequest, CopilotService $service): JsonResponse
    {
        return response()->json(['data' => $service->confirm($copilotRequest, (string) $request->input('token'))]);
    }
}
