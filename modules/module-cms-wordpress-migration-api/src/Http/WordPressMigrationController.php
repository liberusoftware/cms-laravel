<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigrationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\WordPressMigration\Models\WordPressMigration;
use Liberu\Cms\WordPressMigration\Queries\WordPressMigrationQuery;
use Liberu\Cms\WordPressMigration\Services\WordPressMigrationService;
use Liberu\Cms\WordPressMigrationApi\Http\Resources\WordPressMigrationRecordResource;
use Liberu\Cms\WordPressMigrationApi\Http\Resources\WordPressMigrationResource;

final class WordPressMigrationController
{
    public function index(Request $request, WordPressMigrationQuery $query): JsonResponse
    {
        $page = $query->migrations($request->integer('per_page', 15));

        return response()->json(['data' => array_map(WordPressMigrationResource::make(...), $page->items()), 'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'per_page' => $page->perPage(), 'total' => $page->total()]]);
    }

    public function create(Request $request, WordPressMigrationService $service): JsonResponse
    {
        $data = $request->validate(['source_url' => ['sometimes', 'nullable', 'url', 'max:2048'], 'options' => ['sometimes', 'array']]);

        return response()->json(['data' => WordPressMigrationResource::make($service->start($data['source_url'] ?? null, $data['options'] ?? []))], 201);
    }

    private function find(string $publicId, WordPressMigrationQuery $query): WordPressMigration
    {
        $migration = WordPressMigration::query()->where('public_id', $publicId)->first();
        abort_unless($migration && $query->migration($migration->id), 404);

        return $migration;
    }

    public function show(string $publicId, WordPressMigrationQuery $query): JsonResponse
    {
        return response()->json(['data' => WordPressMigrationResource::make($this->find($publicId, $query))]);
    }

    public function records(string $publicId, Request $request, WordPressMigrationQuery $query): JsonResponse
    {
        $migration = $this->find($publicId, $query);
        $page = $query->records($migration, $request->integer('per_page', 25));

        return response()->json(['data' => array_map(WordPressMigrationRecordResource::make(...), $page->items()), 'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()]]);
    }

    public function addRecord(string $publicId, Request $request, WordPressMigrationQuery $query, WordPressMigrationService $service): JsonResponse
    {
        $migration = $this->find($publicId, $query);
        $data = $request->validate(['record_type' => ['required', 'string'], 'source_id' => ['required', 'string', 'max:255'], 'source_parent_id' => ['sometimes', 'nullable', 'string', 'max:255'], 'payload' => ['sometimes', 'array'], 'source_identifiers' => ['sometimes', 'array']]);
        $record = $service->addRecord($migration, $data['record_type'], $data['source_id'], $data['payload'] ?? [], $data['source_identifiers'] ?? [], $data['source_parent_id'] ?? null);

        return response()->json(['data' => WordPressMigrationRecordResource::make($record)], 201);
    }

    public function process(string $publicId, int|string $record, Request $request, WordPressMigrationQuery $query, WordPressMigrationService $service): JsonResponse
    {
        $migration = $this->find($publicId, $query);
        $model = $migration->records()->whereKey($record)->first();
        abort_unless($model, 404);
        $data = $request->validate(['success' => ['required', 'boolean'], 'failure_reason' => ['sometimes', 'nullable', 'string']]);

        return response()->json(['data' => WordPressMigrationRecordResource::make($service->processRecord($model, $data['success'], $data['failure_reason'] ?? null))]);
    }

    public function complete(string $publicId, WordPressMigrationQuery $query, WordPressMigrationService $service): JsonResponse
    {
        return response()->json(['data' => WordPressMigrationResource::make($service->complete($this->find($publicId, $query)))]);
    }

    public function fail(string $publicId, Request $request, WordPressMigrationQuery $query, WordPressMigrationService $service): JsonResponse
    {
        $data = $request->validate(['failure_reason' => ['required', 'string', 'max:2000']]);

        return response()->json(['data' => WordPressMigrationResource::make($service->fail($this->find($publicId, $query), $data['failure_reason']))]);
    }
}
