<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudioApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class MediaAssetResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->public_id, 'type' => 'cms-media-asset', 'title' => $this->resource->title, 'kind' => $this->resource->kind, 'source_type' => $this->resource->source_type, 'mime_type' => $this->resource->mime_type, 'duration_seconds' => $this->resource->duration_seconds, 'stream_uri' => $this->resource->stream_uri, 'poster_uri' => $this->resource->poster_uri, 'status' => $this->resource->status, 'metadata' => $this->resource->metadata, 'tracks' => MediaTrackResource::collection($this->whenLoaded('tracks'))];
    }
}
