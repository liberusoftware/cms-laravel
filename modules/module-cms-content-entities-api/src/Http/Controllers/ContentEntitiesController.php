<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\ContentTypes\Actions\ContentEntryMutationService;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Http\Resources\ContentEntryResource;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Queries\PublishedEntityQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class ContentEntitiesController
{
    public function __construct(
        private PublishedEntityQuery $entities,
        private ContentEntryRepositoryInterface $entries,
        private ContentEntryMutationService $mutations,
    ) {}

    public function index(string $type): AnonymousResourceCollection
    {
        return ContentEntryResource::collection(
            $this->entities->forType($type, request()->integer('per_page', 15), (string) request()->string('q')),
        );
    }

    public function show(string $type, string $slug): ContentEntryResource
    {
        $entry = $this->entities->find($type, $slug);

        if (! $entry instanceof ContentEntry) {
            throw new NotFoundHttpException;
        }

        return new ContentEntryResource($entry);
    }

    public function cloneEntity(Request $request, string $type, int $id): JsonResponse
    {
        $entry = $this->entries->find($id);

        if (! $entry instanceof ContentEntry || $entry->contentType() !== $type) {
            throw new NotFoundHttpException;
        }

        $data = $request->validate(['title' => ['sometimes', 'nullable', 'string', 'max:255']]);
        $clone = $this->mutations->clone($entry, $data['title'] ?? null);

        return ContentEntryResource::make($clone)->response()->setStatusCode(201);
    }
}
