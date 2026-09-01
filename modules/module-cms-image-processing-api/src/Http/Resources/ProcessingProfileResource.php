<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessingApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ImageProcessing\Models\ProcessingProfile;
use LogicException;

final class ProcessingProfileResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof ProcessingProfile) {
            throw new LogicException('ProcessingProfileResource requires a ProcessingProfile instance.');
        }
        $profile = $this->resource;

        return ['id' => $profile->public_id, 'type' => 'cms-image-processing-profiles', 'key' => $profile->key, 'format' => $profile->format, 'quality' => $profile->quality, 'width' => $profile->width, 'height' => $profile->height, 'fit' => $profile->fit, 'created_at' => $profile->created_at?->toISOString(), 'updated_at' => $profile->updated_at?->toISOString()];
    }
}
