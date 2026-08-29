<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegrationApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class AnalyticsEventResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-analytics-event', 'event_type' => $this->resource->event_type, 'event_name' => $this->resource->event_name, 'subject_type' => $this->resource->subject_type, 'subject_id' => $this->resource->subject_id, 'consent_category' => $this->resource->consent_category, 'consent_granted' => $this->resource->consent_granted, 'status' => $this->resource->status, 'occurred_at' => $this->resource->occurred_at?->toISOString()];
    }
}
