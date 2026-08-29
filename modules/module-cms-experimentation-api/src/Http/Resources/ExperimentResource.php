<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperimentationApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class ExperimentResource extends JsonResource
{
    public function toArray($request): array
    {
        $experiment = $this->resource;

        return ['id' => (string) $experiment->getKey(), 'type' => 'cms-experimentation', 'key' => $experiment->key, 'name' => $experiment->name, 'type_name' => $experiment->type, 'status' => $experiment->status, 'allocation_percentage' => $experiment->allocation_percentage, 'winner_variant_key' => $experiment->winner_variant_key, 'goals' => $experiment->goals, 'guardrails' => $experiment->guardrails, 'analysis_policy' => $experiment->analysis_policy, 'variants' => $experiment->variants?->map(fn ($variant): array => ['id' => (string) $variant->id, 'key' => $variant->key, 'name' => $variant->name, 'content' => $variant->content, 'weight' => $variant->weight])->values(), 'created_at' => $experiment->created_at?->toISOString(), 'updated_at' => $experiment->updated_at?->toISOString()];
    }
}
