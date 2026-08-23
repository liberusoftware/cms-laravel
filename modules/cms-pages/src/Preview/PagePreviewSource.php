<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Preview;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Contracts\Preview\PreviewableSourceInterface;
use Liberu\Cms\Pages\Contracts\PageRepositoryInterface;
use Liberu\Cms\Pages\Http\Resources\PageResource;

/**
 * Lets a Page be previewed before publication: it looks the page up by id in any
 * workflow state (tenant-scoped) and renders it through the Delivery API resource.
 */
final readonly class PagePreviewSource implements PreviewableSourceInterface
{
    public function __construct(private PageRepositoryInterface $pages) {}

    public function typeKey(): string
    {
        return 'pages';
    }

    public function find(int $id): ?Model
    {
        return $this->pages->find($id);
    }

    public function toResource(Model $model): JsonResource
    {
        return PageResource::make($model);
    }
}
