<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Core\Models\Channel;

/** @extends JsonResource<Channel> */
/** @mixin Channel */
final class CoreChannelResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $channel = $this->resource;
        if (! $channel instanceof Channel) {
            throw new \UnexpectedValueException('Core channel resource requires a Channel model.');
        }

        return [
            'id' => (string) $channel->id,
            'type' => 'cms-channel',
            'site_id' => (string) $channel->site_id,
            'key' => $channel->key,
            'name' => $channel->name,
            'channel_type' => $channel->type,
            'settings' => $channel->settings,
            'created_at' => $channel->created_at?->toAtomString(),
            'updated_at' => $channel->updated_at?->toAtomString(),
        ];
    }
}
