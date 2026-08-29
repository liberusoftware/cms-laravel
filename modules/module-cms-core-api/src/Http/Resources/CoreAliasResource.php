<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Core\Models\ContentAlias;

/** @extends JsonResource<ContentAlias> */
/** @mixin ContentAlias */
final class CoreAliasResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $alias = $this->resource;
        if (! $alias instanceof ContentAlias) {
            throw new \UnexpectedValueException('Core alias resource requires a ContentAlias model.');
        }

        return [
            'id' => (string) $alias->id,
            'type' => 'cms-content-alias',
            'site_id' => (string) $alias->site_id,
            'channel_id' => $alias->channel_id === null ? null : (string) $alias->channel_id,
            'alias' => $alias->alias,
            'target_type' => $alias->target_type,
            'target_id' => (string) $alias->target_id,
            'redirect_status' => $alias->redirect_status,
            'created_at' => $alias->created_at?->toAtomString(),
            'updated_at' => $alias->updated_at?->toAtomString(),
        ];
    }
}
