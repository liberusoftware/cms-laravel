<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Preview;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Contracts\Preview\PreviewableSourceInterface;
use Liberu\Cms\Posts\Contracts\PostRepositoryInterface;
use Liberu\Cms\Posts\Http\Resources\PostResource;

/**
 * Lets a Post be previewed before publication: it looks the post up by id in any
 * workflow state (tenant-scoped) and renders it, with its taxonomy, through the
 * Delivery API resource.
 */
final readonly class PostPreviewSource implements PreviewableSourceInterface
{
    public function __construct(private PostRepositoryInterface $posts) {}

    public function typeKey(): string
    {
        return 'posts';
    }

    public function find(int $id): ?Model
    {
        return $this->posts->find($id)?->loadMissing(['categories', 'tags']);
    }

    public function toResource(Model $model): JsonResource
    {
        return PostResource::make($model);
    }
}
