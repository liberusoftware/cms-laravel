<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagementApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\DocumentManagement\Models\Document;
use Liberu\Cms\DocumentManagement\Services\DocumentManagementService;

final class DocumentsController
{
    public function index(Request $request, DocumentManagementService $service): JsonResponse
    {
        return response()->json(['data' => $service->documents($request->user()?->current_team_id, $request->integer('page_size', 25))]);
    }

    public function store(Request $request, DocumentManagementService $service): JsonResponse
    {
        return response()->json(['data' => $service->create($this->normalized($request->validate(['title' => ['required', 'string'], 'slug' => ['required', 'string'], 'path' => ['nullable', 'string'], 'mime_type' => ['nullable', 'string'], 'size' => ['nullable', 'integer', 'min:0']])), $request->user()?->current_team_id)], 201);
    }

    public function status(Request $request, Document $document, DocumentManagementService $service): JsonResponse
    {
        abort_unless($document->team_id === $request->user()?->current_team_id, 404);
        $data = $this->normalized($request->validate(['status' => ['required', 'in:draft,processing,ready,archived,failed']]));

        return response()->json(['data' => $service->transition($document, is_string($data['status'] ?? null) ? $data['status'] : 'draft')]);
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
