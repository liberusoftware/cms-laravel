<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistantApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ExperienceAssistant\Models\ExperienceSuggestion;
use LogicException;

final class ExperienceSuggestionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof ExperienceSuggestion) {
            throw new LogicException('ExperienceSuggestionResource requires an ExperienceSuggestion instance.');
        }
        $suggestion = $this->resource;

        return ['id' => $suggestion->public_id, 'type' => 'cms-experience-assistant-suggestions', 'surface' => $suggestion->surface, 'definition' => $suggestion->definition, 'constraints' => $suggestion->constraints ?? [], 'diagnostics' => $suggestion->diagnostics ?? [], 'status' => $suggestion->status, 'reviewer_key' => $suggestion->reviewer_key, 'approved_at' => $suggestion->approved_at?->toISOString(), 'created_at' => $suggestion->created_at?->toISOString(), 'updated_at' => $suggestion->updated_at?->toISOString()];
    }
}
