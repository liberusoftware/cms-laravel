<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Media;

/**
 * A stored media item, exposed to other modules as a read-only value.
 *
 * Content modules reference media by key and resolve the item through the
 * MediaRepository; they never touch the media model, disk, or storage backend.
 *
 * @api This interface is part of the public extension API.
 */
interface MediaItemInterface
{
    /**
     * The media item's stable identifier.
     */
    public function mediaId(): int|string;

    /**
     * The filesystem disk the file lives on.
     */
    public function disk(): string;

    /**
     * The path to the file on its disk.
     */
    public function path(): string;

    /**
     * A publicly resolvable URL for the file.
     */
    public function url(): string;

    /**
     * The original, human-readable file name.
     */
    public function fileName(): string;

    public function mimeType(): string;

    /**
     * Size in bytes.
     */
    public function size(): int;

    /**
     * The folder the item is organised under, or null for the root.
     */
    public function folder(): ?string;

    /**
     * Arbitrary stored metadata (dimensions, alt text, tags, …).
     *
     * @return array<string, mixed>
     */
    public function metadata(): array;
}
