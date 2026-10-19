<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApiApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\HeadlessApi\Models\PersistedQuery;
use LogicException;

final class PersistedQueryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof PersistedQuery) {
            throw new LogicException('PersistedQueryResource requires a PersistedQuery instance.');
        }

        return ['id' => (string) $this->resource->id, 'type' => 'cms-headless-persisted-queries', 'hash' => $this->resource->query_hash, 'query' => $this->resource->query_body, 'last_used_at' => $this->resource->last_used_at?->toISOString(), 'created_at' => $this->resource->created_at?->toISOString(), 'updated_at' => $this->resource->updated_at?->toISOString()];
    }
}
