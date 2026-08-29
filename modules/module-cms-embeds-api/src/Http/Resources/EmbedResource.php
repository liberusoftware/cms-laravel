<?php

namespace Liberu\Cms\EmbedsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmbedResource extends JsonResource
{
    public function toArray($request): array
    {
        $e = $this->resource;

        return ['id' => (string) $e->getKey(), 'type' => 'cms-embeds', 'provider' => $e->provider?->key, 'external_key' => $e->external_key, 'title' => $e->title, 'url' => $e->url, 'privacy_mode' => $e->privacy_mode, 'fallback_url' => $e->fallback_url, 'aspect_ratio' => $e->aspect_ratio, 'responsive' => (bool) $e->responsive, 'status' => $e->status, 'metadata' => $e->metadata];
    }
}
