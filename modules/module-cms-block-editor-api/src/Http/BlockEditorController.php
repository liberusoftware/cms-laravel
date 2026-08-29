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
        $data = $request->validate(['blocks' => ['required', 'array'], 'expected_version' => ['sometimes', 'integer']]);

        return response()->json(['data' => $service->save(null, $subjectType, $subjectId, $data['blocks'], $data['expected_version'] ?? null)]);
    }

    public function lock(BlockDocument $document, Request $request, BlockEditorService $service): JsonResponse
    {
        return response()->json(['data' => $service->lock($document, $request->boolean('locked', true))]);
    }

    public function pattern(Request $request, BlockEditorService $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:180'], 'blocks' => ['required', 'array'], 'reusable' => ['sometimes', 'boolean']]);

        return response()->json(['data' => $service->createPattern(null, $data['name'], $data['blocks'], $data['reusable'] ?? true)], 201);
    }
}
