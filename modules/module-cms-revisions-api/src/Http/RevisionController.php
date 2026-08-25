<?php

declare(strict_types=1);

namespace Liberu\Cms\RevisionsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Content\Revisions\Revision;
use Liberu\Cms\Revisions\Services\RevisionService;

final class RevisionController
{
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
}
