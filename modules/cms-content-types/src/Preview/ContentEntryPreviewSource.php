<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Preview;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Http\Resources\ContentEntryResource;
use Liberu\Cms\Contracts\Preview\PreviewableSourceInterface;

/**
 * Lets a Content-Entry be previewed before publication: it looks the entry up by
 * id in any workflow state (tenant-scoped) and renders it, with its type, through
 * the Delivery API resource.
 */
final readonly class ContentEntryPreviewSource implements PreviewableSourceInterface
{
    public function __construct(private ContentEntryRepositoryInterface $entries) {}

    public function typeKey(): string
    {
        return 'content-entries';
    }

    public function find(int $id): ?Model
    {
        return $this->entries->find($id)?->loadMissing('type');
    }

    public function toResource(Model $model): JsonResource
    {
        return ContentEntryResource::make($model);
    }
}
