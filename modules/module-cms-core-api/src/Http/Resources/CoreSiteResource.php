<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Core\Models\Site;

/** @extends JsonResource<Site> */
/** @mixin Site */
final class CoreSiteResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $site = $this->resource;
        if (! $site instanceof Site) {
            throw new \UnexpectedValueException('Core site resource requires a Site model.');
        }

        return [
            'id' => (string) $site->id,
            'type' => 'cms-site',
            'key' => $site->key,
            'name' => $site->name,
            'domain' => $site->domain,
            'default_locale' => $site->default_locale,
            'timezone' => $site->timezone,
            'status' => $site->status,
            'settings' => $site->settings,
            'channels' => CoreChannelResource::collection($this->whenLoaded('channels')),
            'created_at' => $site->created_at?->toAtomString(),
            'updated_at' => $site->updated_at?->toAtomString(),
        ];
    }
}
