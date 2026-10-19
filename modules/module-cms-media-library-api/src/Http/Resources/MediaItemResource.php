<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaLibraryApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Contracts\Media\MediaItemInterface;

/** @mixin MediaItemInterface */
final class MediaItemResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var MediaItemInterface $item */
        $item = $this->resource;

        return [
            'id' => (string) $item->mediaId(),
            'type' => 'cms-media-library',
            'attributes' => [
                'file_name' => $item->fileName(),
                'mime_type' => $item->mimeType(),
                'size' => $item->size(),
                'folder' => $item->folder(),
                'metadata' => $item->metadata(),
                'url' => $item->url(),
            ],
        ];
    }
}
