<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegrationApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ForumsIntegration\Models\ForumReference;
use LogicException;

final class ForumReferenceResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof ForumReference) {
            throw new LogicException('ForumReferenceResource requires a ForumReference instance.');
        }
        $reference = $this->resource;

        return ['id' => $reference->public_id, 'type' => 'cms-forum-references', 'provider' => $reference->provider, 'external_type' => $reference->external_type, 'external_id' => $reference->external_id, 'url' => $reference->url, 'metadata' => $reference->metadata ?? [], 'last_activity_at' => $reference->last_activity_at?->toISOString(), 'created_at' => $reference->created_at?->toISOString(), 'updated_at' => $reference->updated_at?->toISOString()];
    }
}
