<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistantApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\MediaAssistant\Models\MediaSuggestion;
use LogicException;

final class MediaSuggestionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof MediaSuggestion) {
            throw new LogicException('MediaSuggestionResource requires a MediaSuggestion instance.');
        }

        $suggestion = $this->resource;

        return ['id' => $suggestion->public_id, 'type' => 'cms-media-assistant-suggestions', 'asset_key' => $suggestion->asset_key, 'kind' => $suggestion->kind, 'value' => $suggestion->value, 'provider' => $suggestion->provider, 'model' => $suggestion->model, 'confidence' => $suggestion->confidence, 'provenance' => $suggestion->provenance ?? [], 'status' => $suggestion->status, 'reviewer_key' => $suggestion->reviewer_key, 'review_note' => $suggestion->review_note, 'created_at' => $suggestion->created_at?->toISOString(), 'updated_at' => $suggestion->updated_at?->toISOString()];
    }
}
