<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditorApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\BlockEditor\Models\BlockDocument;
use Liberu\Cms\BlockEditor\Services\BlockEditorService;

final class BlockEditorController
{
    public function save(string $subjectType, string $subjectId, Request $request, BlockEditorService $service): JsonResponse
    {
        $raw = $request->validate(['blocks' => ['required', 'array'], 'expected_version' => ['sometimes', 'integer']]);
        $data = is_array($raw) ? $raw : [];

        $blocks = is_array($data['blocks'] ?? null) ? $data['blocks'] : [];

        return response()->json(['data' => $service->save($request->user()?->current_team_id, $subjectType, $subjectId, $blocks, is_int($data['expected_version'] ?? null) ? $data['expected_version'] : null)]);
    }

    public function lock(BlockDocument $document, Request $request, BlockEditorService $service): JsonResponse
    {
        abort_unless($document->team_id === $request->user()?->current_team_id, 404);

        return response()->json(['data' => $service->lock($document, $request->boolean('locked', true))]);
    }

    public function pattern(Request $request, BlockEditorService $service): JsonResponse
    {
        $raw = $request->validate(['name' => ['required', 'string', 'max:180'], 'blocks' => ['required', 'array'], 'reusable' => ['sometimes', 'boolean']]);
        $data = is_array($raw) ? $raw : [];

        $blocks = is_array($data['blocks'] ?? null) ? $data['blocks'] : [];

        return response()->json(['data' => $service->createPattern($request->user()?->current_team_id, is_string($data['name'] ?? null) ? $data['name'] : '', $blocks, is_bool($data['reusable'] ?? null) ? $data['reusable'] : true)], 201);
    }
}
