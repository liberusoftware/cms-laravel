<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Http\Requests\StoreContentEntryRequest;
use Liberu\Cms\ContentTypes\Http\Requests\UpdateContentEntryRequest;
use Liberu\Cms\ContentTypes\Http\Resources\ContentEntryResource;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Core\Http\Concerns\WritesWorkflowContent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handles Content-Entry writes on the Delivery API. Requires the `content:write`
 * ability; the tenant is stamped on create and reads are tenant-scoped.
 */
final readonly class ContentEntryWriteController
{
    use WritesWorkflowContent;

    public function __construct(private ContentEntryRepositoryInterface $entries) {}

    public function store(StoreContentEntryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $status = $this->pullStatus($data);

        $entry = ContentEntry::create($data + ['status' => WorkflowState::Draft->value]);

        if ($status !== null && $this->shouldTransition($entry->workflowState(), $status)) {
            $entry->transitionTo($status);
        }

        return ContentEntryResource::make($entry->refresh()->load('type'))->response()->setStatusCode(201);
    }

    public function update(UpdateContentEntryRequest $request, int $id): ContentEntryResource
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

        if ($status !== null && $this->shouldTransition($entry->workflowState(), $status)) {
            $entry->transitionTo($status);
        }

        return ContentEntryResource::make($entry->refresh()->load('type'));
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
}
