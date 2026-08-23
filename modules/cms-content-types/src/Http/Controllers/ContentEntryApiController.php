<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Http\Resources\ContentEntryResource;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Core\Support\ApiPagination;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Serves published Content-Entries of a given type over the Delivery API. The
 * repository reads are tenant-scoped; the published check is applied at this
 * boundary because `ofType`/`findBySlug` ignore workflow state.
 */
final readonly class ContentEntryApiController
{
    public function __construct(private ContentEntryRepositoryInterface $entries) {}

    public function index(string $type): AnonymousResourceCollection
    {
        $entries = array_values(array_filter(
            $this->entries->ofType($type),
            static fn (ContentEntry $entry): bool => $entry->isLive(),
        ));

        return ContentEntryResource::collection(ApiPagination::fromArray($entries));
    }

    public function show(string $type, string $slug): ContentEntryResource
    {
        $entry = $this->entries->findBySlug($slug);

        if (! $entry instanceof ContentEntry || ! $entry->isLive() || $this->typeKey($entry) !== $type) {
            throw new NotFoundHttpException;
        }

        return new ContentEntryResource($entry);
    }

    private function typeKey(ContentEntry $entry): ?string
    {
        $type = $entry->type;

        return $type instanceof ContentType ? $type->key : null;
    }
}
