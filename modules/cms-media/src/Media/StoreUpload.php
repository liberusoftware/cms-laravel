<?php

declare(strict_types=1);

namespace Liberu\Cms\Media\Media;

use Illuminate\Http\UploadedFile;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Events\Media\MediaUploaded;
use Liberu\Cms\Contracts\Media\MediaItemInterface;
use Liberu\Cms\Media\Exceptions\InvalidUpload;
use Liberu\Cms\Media\Models\Media;

/**
 * Validates and stores an uploaded file into the media library.
 *
 * MIME type is derived from the file's contents (not the client's claim) and
 * checked against an allow-list, and size is bounded — secure uploads per
 * OWASP A08. On success a MediaUploaded event is broadcast for listeners such
 * as image processing or search indexing.
 */
final readonly class StoreUpload
{
    /**
     * @param  array<int, string>  $allowedMimeTypes
     */
    public function __construct(
        private EventBusInterface $events,
        private string $disk,
        private int $maxSizeKb,
        private array $allowedMimeTypes,
    ) {}

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __invoke(UploadedFile $file, ?string $folder = null, array $metadata = []): MediaItemInterface
    {
        $this->validate($file);

        $path = $file->store($folder ?? 'media', $this->disk);

        if ($path === false) {
            throw InvalidUpload::corrupt();
        }

        $media = Media::create([
            'disk' => $this->disk,
            'path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $this->mimeType($file),
            'size' => (int) $file->getSize(),
            'folder' => $folder,
            'metadata' => array_merge($this->extractMetadata($file), $metadata),
        ]);

        $this->events->dispatch(new MediaUploaded($media));

        return $media;
    }

    private function validate(UploadedFile $file): void
    {
        if (! $file->isValid()) {
            throw InvalidUpload::corrupt();
        }

        $sizeKb = (int) ceil((int) $file->getSize() / 1024);

        if ($sizeKb > $this->maxSizeKb) {
            throw InvalidUpload::tooLarge($sizeKb, $this->maxSizeKb);
        }

        $mimeType = $this->mimeType($file);

        if (! in_array($mimeType, $this->allowedMimeTypes, true)) {
            throw InvalidUpload::disallowedType($mimeType);
        }
    }

    private function mimeType(UploadedFile $file): string
    {
        return $file->getMimeType() ?? $file->getClientMimeType();
    }

    /**
     * @return array<string, mixed>
     */
    private function extractMetadata(UploadedFile $file): array
    {
        if (! str_starts_with($this->mimeType($file), 'image/')) {
            return [];
        }

        $path = $file->getRealPath();

        if ($path === false) {
            return [];
        }

        $dimensions = @getimagesize($path);

        if ($dimensions === false) {
            return [];
        }

        return ['width' => $dimensions[0], 'height' => $dimensions[1]];
    }
}
