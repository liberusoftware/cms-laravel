<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Media;

/**
 * The read/lifecycle boundary other modules use to work with media.
 *
 * Uploading (which involves framework request types) is a Media-module concern;
 * consumers only need to resolve, list, and detach media by key.
 *
 * @api This interface is part of the public extension API.
 */
interface MediaRepositoryInterface
{
    /**
     * Resolve a media item by key, or null when it does not exist.
     */
    public function find(int|string $id): ?MediaItemInterface;

    /**
     * All media items in a folder (null = the root folder).
     *
     * @return iterable<int, MediaItemInterface>
     */
    public function inFolder(?string $folder = null): iterable;

    /**
     * Permanently remove a media item and its underlying file.
     */
    public function delete(int|string $id): bool;
}
