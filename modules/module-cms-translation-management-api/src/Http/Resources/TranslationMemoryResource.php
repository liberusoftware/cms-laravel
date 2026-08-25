<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class TranslationMemoryResource extends JsonResource
{
    public function toArray($request): array { return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-translation-memory', 'source_locale' => $this->resource->source_locale, 'target_locale' => $this->resource->target_locale, 'source_text' => $this->resource->source_text, 'translated_text' => $this->resource->translated_text, 'status' => $this->resource->status, 'metadata' => $this->resource->metadata]; }
}
