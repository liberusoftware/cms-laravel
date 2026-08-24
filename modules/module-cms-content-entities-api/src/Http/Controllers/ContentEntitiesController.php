<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesApi\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\ContentTypes\Http\Resources\ContentEntryResource;
use Liberu\Cms\ContentTypes\Models\ContentEntry;

final class ContentEntitiesController
{
    public function index(string $type): AnonymousResourceCollection
    {
        return ContentEntryResource::collection(
            ContentEntry::query()
                ->whereRelation('type', 'key', $type)
                ->where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->latest('published_at')
                ->paginate(),
        );
    }
}
