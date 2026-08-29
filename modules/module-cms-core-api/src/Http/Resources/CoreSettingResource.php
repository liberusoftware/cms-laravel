<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Core\Models\Setting;

/** @extends JsonResource<Setting> */
/** @mixin Setting */
final class CoreSettingResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $setting = $this->resource;
        if (! $setting instanceof Setting) {
            throw new \UnexpectedValueException('Core setting resource requires a Setting model.');
        }

        return [
            'id' => (string) $setting->id,
            'type' => 'cms-setting',
            'site_id' => $setting->site_id === null ? null : (string) $setting->site_id,
            'key' => $setting->key,
            'value' => $setting->value,
            'environment' => $setting->environment,
            'created_at' => $setting->created_at?->toAtomString(),
            'updated_at' => $setting->updated_at?->toAtomString(),
        ];
    }
}
