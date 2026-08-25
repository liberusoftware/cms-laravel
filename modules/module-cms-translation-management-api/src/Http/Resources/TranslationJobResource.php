<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class TranslationJobResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->resource->public_id,
            'type' => 'cms-translation-job',
            'name' => $this->resource->name,
            'source_locale' => $this->resource->source_locale,
            'target_locale' => $this->resource->target_locale,
            'status' => $this->resource->status,
            'vendor_key' => $this->resource->vendor_key,
            'total_units' => $this->resource->total_units,
            'completed_units' => $this->resource->completed_units,
            'estimated_cost' => $this->resource->estimated_cost,
            'actual_cost' => $this->resource->actual_cost,
            'currency' => $this->resource->currency,
            'source_changes' => TranslationSourceChangeResource::collection($this->whenLoaded('sourceChanges')),
            'created_at' => $this->resource->created_at?->toISOString(),
            'updated_at' => $this->resource->updated_at?->toISOString(),
        ];
    }
}
