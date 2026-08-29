<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperationsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Liberu\Cms\SecurityOperations\Models\SecurityOperation;
use Liberu\Cms\SecurityOperations\Services\SecurityOperationsService;

final class SecurityOperationsController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => SecurityOperation::query()->when($request->input('kind'), fn ($query, $kind) => $query->where('kind', $kind))->latest()->paginate(min(100, max(1, $request->integer('per_page', 50))))]);
    }

    public function inventory(Request $request, SecurityOperationsService $service): JsonResponse
    {
        return response()->json(['data' => $service->inventory($request->validate(['packages' => ['required', 'array']])['packages'], $request->user()?->id)], 201);
    }

    public function provenance(Request $request, SecurityOperationsService $service): JsonResponse
    {
        $data = $request->validate(['extension' => ['required', 'string'], 'version' => ['required', 'string'], 'source' => ['required', 'url']]);

        return response()->json(['data' => $service->provenance($data['extension'], $data['version'], $data['source'], $request->user()?->id)], 201);
    }

    public function integrity(Request $request, SecurityOperationsService $service): JsonResponse
    {
        $data = $request->validate(['subject' => ['required', 'string'], 'content' => ['required', 'string']]);

        return response()->json(['data' => $service->integrity($data['subject'], $data['content'], $request->user()?->id)], 201);
    }

    public function advisory(Request $request, SecurityOperationsService $service): JsonResponse
    {
        $data = $request->validate(['title' => ['required', 'string'], 'severity' => ['required', 'in:low,medium,high,critical'], 'details' => ['array']]);

        return response()->json(['data' => $service->advisory($data['title'], $data['severity'], $data['details'] ?? [], $request->user()?->id)], 201);
    }

    public function scan(Request $request, SecurityOperationsService $service): JsonResponse
    {
        $file = $request->file('file');
        abort_unless($file instanceof UploadedFile, 422);
        $file->isValid() || abort(422, 'The uploaded file is invalid.');

        return response()->json(['data' => $service->scan($file, $request->user()?->id)], 201);
    }

    public function incident(Request $request, SecurityOperationsService $service): JsonResponse
    {
        $incident = $request->validate(['incident' => ['required', 'string']])['incident'];

        return response()->json(['data' => $service->incidentBundle($incident)]);
    }
}
