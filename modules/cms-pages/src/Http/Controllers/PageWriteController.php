<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Core\Http\Concerns\WritesWorkflowContent;
use Liberu\Cms\Pages\Contracts\PageRepositoryInterface;
use Liberu\Cms\Pages\Http\Requests\StorePageRequest;
use Liberu\Cms\Pages\Http\Requests\UpdatePageRequest;
use Liberu\Cms\Pages\Http\Resources\PageResource;
use Liberu\Cms\Pages\Models\Page;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handles Page writes on the Delivery API. Requires the `content:write` ability.
 * The tenant is stamped from the request context on create, and reads for update
 * and delete are tenant-scoped, so a cross-tenant id is simply not found. A
 * `status` change goes through the editorial workflow.
 */
final readonly class PageWriteController
{
    use WritesWorkflowContent;

    public function __construct(private PageRepositoryInterface $pages) {}

    public function store(StorePageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $status = $this->pullStatus($data);

        $page = Page::create($data + ['status' => WorkflowState::Draft->value]);

        if ($status instanceof WorkflowState && $this->shouldTransition($page->workflowState(), $status)) {
            $page->transitionTo($status);
        }

        return PageResource::make($page->refresh())->response()->setStatusCode(201);
    }

    public function update(UpdatePageRequest $request, int $id): PageResource
    {
        $page = $this->pages->find($id);

        if (! $page instanceof Page) {
            throw new NotFoundHttpException;
        }

        $data = $request->validated();
        $status = $this->pullStatus($data);

        if ($data !== []) {
            $page->update($data);
        }

        if ($status instanceof WorkflowState && $this->shouldTransition($page->workflowState(), $status)) {
            $page->transitionTo($status);
        }

        return PageResource::make($page->refresh());
    }

    public function destroy(int $id): Response
    {
        $page = $this->pages->find($id);

        if (! $page instanceof Page) {
            throw new NotFoundHttpException;
        }

        $page->delete();

        return response()->noContent();
    }
}
