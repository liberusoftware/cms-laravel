<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class TranslationGlossaryResource extends JsonResource
{
    public function toArray($request): array { return ['id' => (string) $this->resource->getKey(), 'type' => 'cms-translation-glossary', 'source_locale' => $this->resource->source_locale, 'target_locale' => $this->resource->target_locale, 'source_term' => $this->resource->source_term, 'preferred_term' => $this->resource->preferred_term, 'forbidden_terms' => $this->resource->forbidden_terms ?? []]; }
}
