<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class TranslationSourceChangeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->resource->getKey(),
            'type' => 'cms-translation-source-change',
            'job_id' => (string) $this->resource->job_id,
            'subject_type' => $this->resource->subject_type,
            'subject_id' => $this->resource->subject_id,
            'field' => $this->resource->field,
            'source_text' => $this->resource->source_text,
            'translated_text' => $this->resource->translated_text,
            'status' => $this->resource->status,
            'provider' => $this->resource->provider,
            'model' => $this->resource->model,
            'cost' => $this->resource->cost,
            'provenance' => $this->resource->provenance,
            'review_notes' => $this->resource->review_notes,
            'translated_at' => $this->resource->translated_at?->toISOString(),
            'reviewed_at' => $this->resource->reviewed_at?->toISOString(),
        ];
    }
}
