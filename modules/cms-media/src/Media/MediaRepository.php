<?php

declare(strict_types=1);

namespace Liberu\Cms\Media\Media;

use Illuminate\Support\Facades\Storage;
use Liberu\Cms\Contracts\Media\MediaItemInterface;
use Liberu\Cms\Contracts\Media\MediaRepositoryInterface;
use Liberu\Cms\Media\Models\Media;

final class MediaRepository implements MediaRepositoryInterface
{
    public function find(int|string $id): ?MediaItemInterface
    {
        return Media::query()->find($id);
    }

    public function inFolder(?string $folder = null): iterable
    {
        return Media::query()
            ->where('folder', $folder)
            ->orderBy('file_name')
            ->get()
            ->all();
    }

    public function delete(int|string $id): bool
    {
        $media = Media::query()->find($id);

        if ($media === null) {
            return false;
        }

        Storage::disk($media->disk)->delete($media->path);

        return (bool) $media->delete();
    }
}
