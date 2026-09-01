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
        $data = $this->normalized($request->validate(['capability' => ['required', 'string'], 'prompt' => ['required', 'string', 'max:10000'], 'input' => ['sometimes', 'array'], 'idempotency_key' => ['sometimes', 'string', 'max:255']]));

        return response()->json(['data' => $service->submit($request->user()?->current_team_id, is_string($data['capability'] ?? null) ? $data['capability'] : '', is_string($data['prompt'] ?? null) ? $data['prompt'] : '', $this->normalized($data['input'] ?? []), is_string($data['idempotency_key'] ?? null) ? $data['idempotency_key'] : null)], 201);
    }

    public function execute(Request $request, CopilotRequest $copilotRequest, CopilotService $service): JsonResponse
    {
        abort_unless($copilotRequest->team_id === $request->user()?->current_team_id, 404);

        return response()->json(['data' => $service->execute($copilotRequest)]);
    }

    public function confirm(Request $request, CopilotRequest $copilotRequest, CopilotService $service): JsonResponse
    {
        abort_unless($copilotRequest->team_id === $request->user()?->current_team_id, 404);
        $token = $request->input('token');

        return response()->json(['data' => $service->confirm($copilotRequest, is_string($token) ? $token : '')]);
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
