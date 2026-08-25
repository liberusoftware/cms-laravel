<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CoreSiteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->getKey(),
            'type' => 'cms-site',
            'key' => $this->key,
            'name' => $this->name,
            'domain' => $this->domain,
            'default_locale' => $this->default_locale,
            'timezone' => $this->timezone,
            'status' => $this->status,
            'settings' => $this->settings,
            'channels' => CoreChannelResource::collection($this->whenLoaded('channels')),
            'created_at' => $this->created_at?->toAtomString(),
            'updated_at' => $this->updated_at?->toAtomString(),
        ];
    }
}
