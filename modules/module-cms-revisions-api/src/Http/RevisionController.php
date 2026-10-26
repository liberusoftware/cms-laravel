<?php

declare(strict_types=1);

namespace Liberu\Cms\RevisionsApi\Http;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Content\Revisions\HasRevisions;
use Liberu\Cms\Content\Revisions\Revision;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Liberu\Cms\Revisions\Services\RevisionService;

final class RevisionController
{
    public function create(Request $request, string $type, int $id, RevisionService $service): JsonResponse
    {
        $this->authorizeSubject($type, $id);

        $data = $request->validate(['snapshot' => ['required', 'array'], 'branch' => ['sometimes', 'string'], 'metadata' => ['array']]);

        return response()->json(['data' => $service->create($type, $id, $data['snapshot'], $request->user()?->getAuthIdentifier(), $data['branch'] ?? 'main', false, $data['metadata'] ?? [])], 201);
    }

    public function autosave(Request $request, string $type, int $id, RevisionService $service): JsonResponse
    {
        $this->authorizeSubject($type, $id);

        $data = $request->validate(['snapshot' => ['required', 'array'], 'branch' => ['sometimes', 'string']]);

        return response()->json(['data' => $service->autosave($type, $id, $data['snapshot'], $request->user()?->getAuthIdentifier(), $data['branch'] ?? 'main')], 201);
    }

    public function index(Request $request, string $type, int $id): JsonResponse
    {
        $this->authorizeSubject($type, $id);

        $items = Revision::query()->where('revisionable_type', $type)->where('revisionable_id', $id)->where('autosave', false)->latest('revision_number')->paginate(min(100, max(1, $request->integer('per_page', 20))));

        return response()->json(['data' => $items]);
    }

    public function restore(Request $request, int $revision, RevisionService $service): JsonResponse
    {
        $item = $service->restore($this->revision($revision), $request->user()?->getAuthIdentifier());

        return response()->json(['data' => $item], 201);
    }

    public function compare(int $from, int $to, RevisionService $service): JsonResponse
    {
        return response()->json(['data' => $service->compare($this->revision($from), $this->revision($to))]);
    }

    public function branch(Request $request, int $revision, RevisionService $service): JsonResponse
    {
        $branch = $request->validate(['branch' => ['required', 'string']])['branch'];

        return response()->json(['data' => $service->branch($this->revision($revision), $branch, $request->user()?->getAuthIdentifier())], 201);
    }

    public function publish(int $revision, RevisionService $service): JsonResponse
    {
        return response()->json(['data' => $service->publish($this->revision($revision))]);
    }

    public function prune(Request $request, string $type, int $id, RevisionService $service): JsonResponse
    {
        $this->authorizeSubject($type, $id);

        $data = $request->validate(['retain' => ['sometimes', 'integer', 'min:1', 'max:1000'], 'branch' => ['sometimes', 'string']]);

        return response()->json(['data' => ['deleted' => $service->prune($type, $id, $data['retain'] ?? 20, $data['branch'] ?? 'main')]]);
    }

    private function revision(int $id): Revision
    {
        $revision = Revision::query()->findOrFail($id);
        $this->authorizeSubject($revision->revisionable_type, (int) $revision->revisionable_id);

        return $revision;
    }

    private function authorizeSubject(string $type, int $id): void
    {
        $tenant = app(TenantContextInterface::class)->tenantId();
        $model = Relation::getMorphedModel($type) ?? $type;
        abort_unless($tenant !== null && is_subclass_of($model, Model::class)
            && in_array(HasRevisions::class, class_uses_recursive($model), true), 404);

        $model::query()->where('team_id', $tenant)->findOrFail($id);
    }
}
