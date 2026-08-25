<?php

declare(strict_types=1);

namespace Liberu\Cms\RevisionsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Content\Revisions\Revision;
use Liberu\Cms\Revisions\Services\RevisionService;

final class RevisionController
{
    public function create(Request $request, string $type, int $id, RevisionService $service): JsonResponse
    {
        $data = $request->validate(['snapshot' => ['required', 'array'], 'branch' => ['sometimes', 'string'], 'metadata' => ['array']]);

        return response()->json(['data' => $service->create($type, $id, $data['snapshot'], $request->user()?->getAuthIdentifier(), $data['branch'] ?? 'main', false, $data['metadata'] ?? [])], 201);
    }

    public function autosave(Request $request, string $type, int $id, RevisionService $service): JsonResponse
    {
        $data = $request->validate(['snapshot' => ['required', 'array'], 'branch' => ['sometimes', 'string']]);

        return response()->json(['data' => $service->autosave($type, $id, $data['snapshot'], $request->user()?->getAuthIdentifier(), $data['branch'] ?? 'main')], 201);
    }

    public function index(Request $request, string $type, int $id): JsonResponse
    {
        $items = Revision::query()->where('revisionable_type', $type)->where('revisionable_id', $id)->where('autosave', false)->latest('revision_number')->paginate((int) $request->integer('per_page', 20));

        return response()->json(['data' => $items]);
    }

    public function restore(Request $request, int $revision, RevisionService $service): JsonResponse
    {
        $item = $service->restore(Revision::query()->findOrFail($revision), $request->user()?->getAuthIdentifier());

        return response()->json(['data' => $item], 201);
    }

    public function compare(int $from, int $to, RevisionService $service): JsonResponse
    {
        return response()->json(['data' => $service->compare(Revision::query()->findOrFail($from), Revision::query()->findOrFail($to))]);
    }

    public function branch(Request $request, int $revision, RevisionService $service): JsonResponse
    {
        $branch = $request->validate(['branch' => ['required', 'string']])['branch'];

        return response()->json(['data' => $service->branch(Revision::query()->findOrFail($revision), $branch, $request->user()?->getAuthIdentifier())], 201);
    }

    public function publish(int $revision, RevisionService $service): JsonResponse
    {
        return response()->json(['data' => $service->publish(Revision::query()->findOrFail($revision))]);
    }

    public function prune(Request $request, string $type, int $id, RevisionService $service): JsonResponse
    {
        $data = $request->validate(['retain' => ['sometimes', 'integer', 'min:1', 'max:1000'], 'branch' => ['sometimes', 'string']]);

        return response()->json(['data' => ['deleted' => $service->prune($type, $id, $data['retain'] ?? 20, $data['branch'] ?? 'main')]]);
    }
}
