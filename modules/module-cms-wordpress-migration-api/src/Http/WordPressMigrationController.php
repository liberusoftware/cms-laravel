<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigrationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\WordPressMigration\Models\WordPressMigration;
use Liberu\Cms\WordPressMigration\Models\WordPressMigrationRecord;
use Liberu\Cms\WordPressMigration\Queries\WordPressMigrationQuery;
use Liberu\Cms\WordPressMigration\Services\WordPressMigrationService;
use Liberu\Cms\WordPressMigrationApi\Http\Resources\WordPressMigrationRecordResource;
use Liberu\Cms\WordPressMigrationApi\Http\Resources\WordPressMigrationResource;

final class WordPressMigrationController
{
    public function index(Request $request, WordPressMigrationQuery $query): JsonResponse
    {
        $page = $query->migrations($request->integer('per_page', 15));

        return response()->json(['data' => array_map(static function (mixed $migration): array {
            if (! $migration instanceof WordPressMigration) {
                throw new \UnexpectedValueException('Invalid migration result.');
            }

            return WordPressMigrationResource::make($migration);
        }, $page->items()), 'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'per_page' => $page->perPage(), 'total' => $page->total()]]);
    }

    public function create(Request $request, WordPressMigrationService $service): JsonResponse
    {
        $data = $this->validated($request, ['source_url' => ['sometimes', 'nullable', 'url', 'max:2048'], 'options' => ['sometimes', 'array']]);

        $sourceUrl = $data['source_url'] ?? null;
        $options = $this->associative($data['options'] ?? []);
        if ($sourceUrl !== null && ! is_string($sourceUrl)) {
            throw new \UnexpectedValueException('Invalid source URL.');
        }

        return response()->json(['data' => WordPressMigrationResource::make($service->start($sourceUrl, $options))], 201);
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

        return response()->json(['data' => array_map(static function (mixed $record): array {
            if (! $record instanceof WordPressMigrationRecord) {
                throw new \UnexpectedValueException('Invalid migration record result.');
            }

            return WordPressMigrationRecordResource::make($record);
        }, $page->items()), 'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()]]);
    }

    public function addRecord(string $publicId, Request $request, WordPressMigrationQuery $query, WordPressMigrationService $service): JsonResponse
    {
        $migration = $this->find($publicId, $query);
        $data = $this->validated($request, ['record_type' => ['required', 'string'], 'source_id' => ['required', 'string', 'max:255'], 'source_parent_id' => ['sometimes', 'nullable', 'string', 'max:255'], 'payload' => ['sometimes', 'array'], 'source_identifiers' => ['sometimes', 'array']]);
        $recordType = $data['record_type'] ?? null;
        $sourceId = $data['source_id'] ?? null;
        $payload = $this->associative($data['payload'] ?? []);
        $sourceIdentifiers = $this->associative($data['source_identifiers'] ?? []);
        $sourceParentId = $data['source_parent_id'] ?? null;
        if (! is_string($recordType) || ! is_string($sourceId) || ($sourceParentId !== null && ! is_string($sourceParentId))) {
            throw new \UnexpectedValueException('Invalid migration record payload.');
        }
        $record = $service->addRecord($migration, $recordType, $sourceId, $payload, $sourceIdentifiers, $sourceParentId);

        return response()->json(['data' => WordPressMigrationRecordResource::make($record)], 201);
    }

    public function process(string $publicId, int|string $record, Request $request, WordPressMigrationQuery $query, WordPressMigrationService $service): JsonResponse
    {
        $migration = $this->find($publicId, $query);
        $model = $migration->records()->whereKey($record)->first();
        if (! $model instanceof WordPressMigrationRecord) {
            abort(404);
        }
        $data = $this->validated($request, ['success' => ['required', 'boolean'], 'failure_reason' => ['sometimes', 'nullable', 'string']]);
        $success = $data['success'] ?? null;
        $failureReason = $data['failure_reason'] ?? null;
        if (! is_bool($success) || ($failureReason !== null && ! is_string($failureReason))) {
            throw new \UnexpectedValueException('Invalid migration processing payload.');
        }

        return response()->json(['data' => WordPressMigrationRecordResource::make($service->processRecord($model, $success, $failureReason))]);
    }

    public function complete(string $publicId, WordPressMigrationQuery $query, WordPressMigrationService $service): JsonResponse
    {
        return response()->json(['data' => WordPressMigrationResource::make($service->complete($this->find($publicId, $query)))]);
    }

    public function fail(string $publicId, Request $request, WordPressMigrationQuery $query, WordPressMigrationService $service): JsonResponse
    {
        $data = $this->validated($request, ['failure_reason' => ['required', 'string', 'max:2000']]);
        $reason = $data['failure_reason'] ?? null;
        if (! is_string($reason)) {
            throw new \UnexpectedValueException('Invalid migration failure reason.');
        }

        return response()->json(['data' => WordPressMigrationResource::make($service->fail($this->find($publicId, $query), $reason))]);
    }

    /**
     * @param  array<string, array<int, mixed>>  $rules
     * @return array<string, mixed>
     */
    private function validated(Request $request, array $rules): array
    {
        $data = $request->validate($rules);
        if (! is_array($data)) {
            return [];
        }

        $validated = [];
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $validated[$key] = $value;
            }
        }

        return $validated;
    }

    /** @return array<string, mixed> */
    private function associative(mixed $value): array
    {
        if (! is_array($value)) {
            throw new \UnexpectedValueException('An associative object is required.');
        }

        $result = [];
        foreach ($value as $key => $item) {
            if (! is_string($key)) {
                throw new \UnexpectedValueException('Object keys must be strings.');
            }
            $result[$key] = $item;
        }

        return $result;
    }
}
