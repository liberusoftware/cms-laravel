<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Liberu\Cms\ContentEntitiesApi\Http\Resources\ContentEntityResource;
use Liberu\Cms\ContentEntitiesApi\Http\Resources\LegacyContentEntryResource;
use Liberu\Cms\ContentTypes\Actions\ContentEntryMutationService;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Http\Requests\StoreContentEntryRequest;
use Liberu\Cms\ContentTypes\Http\Requests\UpdateContentEntryRequest;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Queries\PublishedEntityQuery;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Core\Http\Concerns\WritesWorkflowContent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class ContentEntitiesController
{
    use WritesWorkflowContent;

    public function __construct(
        private PublishedEntityQuery $entities,
        private ContentEntryRepositoryInterface $entries,
        private ContentEntryMutationService $mutations,
    ) {}

    public function index(string $type): AnonymousResourceCollection
    {
        return ContentEntityResource::collection(
            $this->entities->forType($type, request()->integer('per_page', 15), (string) request()->string('q')),
        );
    }

    public function show(string $type, string $slug): ContentEntityResource
    {
        $entry = $this->entities->find($type, $slug);

        if (! $entry instanceof ContentEntry) {
            throw new NotFoundHttpException;
        }

        return new ContentEntityResource($entry);
    }

    public function legacyIndex(string $type): AnonymousResourceCollection
    {
        return LegacyContentEntryResource::collection(
            $this->entities->forType($type, request()->integer('per_page', 15), (string) request()->string('q')),
        );
    }

    public function legacyShow(string $type, string $slug): LegacyContentEntryResource
    {
        $entry = $this->entities->find($type, $slug);
        if (! $entry instanceof ContentEntry) {
            throw new NotFoundHttpException;
        }

        return new LegacyContentEntryResource($entry);
    }

    public function store(StoreContentEntryRequest $request): ContentEntityResource
    {
        $data = $request->validated();
        $status = $this->pullStatus($data);
        $entry = ContentEntry::create($data + ['status' => WorkflowState::Draft->value]);
        if ($status instanceof WorkflowState && $this->shouldTransition($entry->workflowState(), $status)) {
            $entry->transitionTo($status);
        }

        return new ContentEntityResource($entry->refresh()->load('type'));
    }

    public function legacyStore(StoreContentEntryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $status = $this->pullStatus($data);
        $entry = ContentEntry::create($data + ['status' => WorkflowState::Draft->value]);
        if ($status instanceof WorkflowState && $this->shouldTransition($entry->workflowState(), $status)) {
            $entry->transitionTo($status);
        }

        return (new LegacyContentEntryResource($entry->refresh()->load('type')))->response()->setStatusCode(201);
    }

    public function update(UpdateContentEntryRequest $request, int $id): ContentEntityResource
    {
        $entry = $this->entries->find($id);
        if (! $entry instanceof ContentEntry) {
            throw new NotFoundHttpException;
        }
        $data = $request->validated();
        $status = $this->pullStatus($data);
        if ($data !== []) {
            $entry->update($data);
        }
        if ($status instanceof WorkflowState && $this->shouldTransition($entry->workflowState(), $status)) {
            $entry->transitionTo($status);
        }

        return new ContentEntityResource($entry->refresh()->load('type'));
    }

    public function legacyUpdate(UpdateContentEntryRequest $request, int $id): LegacyContentEntryResource
    {
        $entry = $this->entries->find($id);
        if (! $entry instanceof ContentEntry) {
            throw new NotFoundHttpException;
        }
        $data = $request->validated();
        $status = $this->pullStatus($data);
        if ($data !== []) {
            $entry->update($data);
        }
        if ($status instanceof WorkflowState && $this->shouldTransition($entry->workflowState(), $status)) {
            $entry->transitionTo($status);
        }

        return new LegacyContentEntryResource($entry->refresh()->load('type'));
    }

    public function destroy(int $id): Response
    {
        $entry = $this->entries->find($id);
        if (! $entry instanceof ContentEntry) {
            throw new NotFoundHttpException;
        }
        $entry->delete();

        return response()->noContent();
    }

    public function cloneEntity(Request $request, string $type, int $id): JsonResponse
    {
        $entry = $this->entries->find($id);

        if (! $entry instanceof ContentEntry || $entry->contentType() !== $type) {
            throw new NotFoundHttpException;
        }

        $data = $request->validate(['title' => ['sometimes', 'nullable', 'string', 'max:255']]);
        $title = is_array($data) ? ($data['title'] ?? null) : null;
        $clone = $this->mutations->clone($entry, is_string($title) ? $title : null);

        return (new ContentEntityResource($clone))->response()->setStatusCode(201);
    }
}
