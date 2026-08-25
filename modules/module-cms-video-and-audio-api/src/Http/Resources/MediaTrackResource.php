<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudioApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class MediaTrackResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-media-track', 'track_type' => $this->resource->track_type, 'language' => $this->resource->language, 'label' => $this->resource->label, 'uri' => $this->resource->uri, 'content' => $this->resource->content, 'start_seconds' => $this->resource->start_seconds, 'end_seconds' => $this->resource->end_seconds, 'metadata' => $this->resource->metadata];
    }
}
