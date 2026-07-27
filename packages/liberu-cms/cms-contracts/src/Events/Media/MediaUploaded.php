<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Events\Media;

use Liberu\Cms\Contracts\Events\CmsEvent;
use Liberu\Cms\Contracts\Media\MediaItemInterface;

/**
 * Emitted when a file has been stored in the media library. Image processing,
 * search indexing, and CDN warmers listen for this without importing the Media
 * module's internals.
 *
 * @api This class is part of the public extension API.
 */
final readonly class MediaUploaded implements CmsEvent
{
    public function __construct(public MediaItemInterface $media) {}

    public function name(): string
    {
        return 'media.uploaded';
    }
}
