<?php

declare(strict_types=1);

namespace Liberu\Cms\LocalizationApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Localization\Models\Locale;
use Liberu\Cms\Localization\Models\LocaleVariant;
use LogicException;

final class LocalizationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if ($this->resource instanceof Locale) {
            $key = $this->resource->getKey();

            return ['id' => is_int($key) || is_string($key) ? (string) $key : '', 'type' => 'cms-locales', 'locale' => $this->resource->locale, 'fallback_locale' => $this->resource->fallback_locale, 'direction' => $this->resource->direction, 'enabled' => (bool) $this->resource->enabled];
        }
        if ($this->resource instanceof LocaleVariant) {
            return ['id' => $this->resource->public_id, 'type' => 'cms-localization-variants', 'source_type' => $this->resource->source_type, 'source_key' => $this->resource->source_key, 'field' => $this->resource->field, 'locale' => $this->resource->locale, 'value' => $this->resource->value, 'localized_slug' => $this->resource->localized_slug, 'status' => $this->resource->status, 'completed_at' => $this->resource->completed_at?->toISOString()];
        }
        throw new LogicException('LocalizationResource requires a localization model.');
    }
}
