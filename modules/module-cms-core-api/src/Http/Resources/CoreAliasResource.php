<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CoreAliasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->getKey(),
            'type' => 'cms-content-alias',
            'site_id' => (string) $this->site_id,
            'channel_id' => $this->channel_id === null ? null : (string) $this->channel_id,
            'alias' => $this->alias,
            'target_type' => $this->target_type,
            'target_id' => (string) $this->target_id,
            'redirect_status' => (int) $this->redirect_status,
            'created_at' => $this->created_at?->toAtomString(),
            'updated_at' => $this->updated_at?->toAtomString(),
        ];
    }
}
