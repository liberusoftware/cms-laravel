<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistantApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class StyleRuleResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-translation-style-rule', 'locale' => $this->resource->locale, 'name' => $this->resource->name, 'pattern' => $this->resource->pattern, 'message' => $this->resource->message, 'severity' => $this->resource->severity];
    }
}
