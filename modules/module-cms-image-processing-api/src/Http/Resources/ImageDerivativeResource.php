<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessingApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\ImageProcessing\Models\ImageDerivative;
use LogicException;

final class ImageDerivativeResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        if (! $this->resource instanceof ImageDerivative) {
            throw new LogicException('ImageDerivativeResource requires an ImageDerivative instance.');
        }
        $derivative = $this->resource;

        return ['id' => $derivative->public_id, 'type' => 'cms-image-processing-derivatives', 'asset_key' => $derivative->asset_key, 'profile_id' => $derivative->profile_id, 'source_checksum' => $derivative->source_checksum, 'path' => $derivative->path, 'status' => $derivative->status, 'metadata' => $derivative->metadata ?? [], 'created_at' => $derivative->created_at?->toISOString(), 'updated_at' => $derivative->updated_at?->toISOString()];
    }
}
