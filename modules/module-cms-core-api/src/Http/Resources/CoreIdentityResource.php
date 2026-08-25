<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CoreIdentityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->getKey(),
            'type' => 'cms-content-identity',
            'site_id' => (string) $this->site_id,
            'channel_id' => $this->channel_id === null ? null : (string) $this->channel_id,
            'content_type' => $this->content_type,
            'content_id' => (string) $this->content_id,
            'canonical_path' => $this->canonical_path,
            'status' => $this->status,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toAtomString(),
            'updated_at' => $this->updated_at?->toAtomString(),
        ];
    }
}
