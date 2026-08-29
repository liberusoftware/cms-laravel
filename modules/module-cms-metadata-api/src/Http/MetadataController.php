<?php

declare(strict_types=1);

namespace Liberu\Cms\MetadataApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Metadata\Services\MetadataService;

final class MetadataController
{
    public function index(string $subjectType, string $subjectId, MetadataService $metadata): JsonResponse
    {
        return response()->json(['data' => $metadata->all($subjectType, $subjectId)]);
    }

    public function set(Request $request, string $subjectType, string $subjectId, MetadataService $metadata): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:120'], 'value' => ['present']]);
        $entry = $metadata->set($subjectType, $subjectId, (string) $data['key'], $data['value']);

        return response()->json(['data' => ['key' => $entry->key, 'value' => $entry->value, 'value_type' => $entry->value_type]], 201);
    }

    public function remove(string $subjectType, string $subjectId, string $key, MetadataService $metadata): JsonResponse
    {
        $metadata->remove($subjectType, $subjectId, $key);

        return response()->json([], 204);
    }
}
