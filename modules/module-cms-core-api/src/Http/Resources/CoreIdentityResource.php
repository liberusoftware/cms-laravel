<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Core\Models\ContentIdentity;

/** @extends JsonResource<ContentIdentity> */
/** @mixin ContentIdentity */
final class CoreIdentityResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $identity = $this->resource;
        if (! $identity instanceof ContentIdentity) {
            throw new \UnexpectedValueException('Core identity resource requires a ContentIdentity model.');
        }

        return [
            'id' => (string) $identity->id,
            'type' => 'cms-content-identity',
            'site_id' => (string) $identity->site_id,
            'channel_id' => $identity->channel_id === null ? null : (string) $identity->channel_id,
            'content_type' => $identity->content_type,
            'content_id' => (string) $identity->content_id,
            'canonical_path' => $identity->canonical_path,
            'status' => $identity->status,
            'metadata' => $identity->metadata,
            'created_at' => $identity->created_at?->toAtomString(),
            'updated_at' => $identity->updated_at?->toAtomString(),
        ];
    }
}
