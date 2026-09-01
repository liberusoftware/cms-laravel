<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegrationApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\AnalyticsIntegration\Models\AnalyticsEvent;
use LogicException;

final class AnalyticsEventResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof AnalyticsEvent) {
            throw new LogicException('AnalyticsEventResource requires an AnalyticsEvent.');
        }
        $key = $this->resource->getKey();

        return ['id' => is_int($key) || is_string($key) ? (string) $key : '', 'type' => 'cms-analytics-event', 'event_type' => $this->resource->event_type, 'event_name' => $this->resource->event_name, 'subject_type' => $this->resource->subject_type, 'subject_id' => $this->resource->subject_id, 'consent_category' => $this->resource->consent_category, 'consent_granted' => $this->resource->consent_granted, 'status' => $this->resource->status, 'occurred_at' => $this->resource->occurred_at?->toISOString()];
    }
}
