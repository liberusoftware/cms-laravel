<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Http\Concerns;

use Liberu\Cms\Contracts\Media\MediaItemInterface;

/**
 * Shared wire shape for a featured media item embedded in a Delivery API
 * Resource: a public URL and its alt text, or null when there is no image. Kept
 * in one place so the media representation cannot drift between the modules that
 * embed it.
 */
trait EmbedsFeaturedMedia
{
    /**
     * @return array{url: string, alt: mixed}|null
     */
    protected function featuredMediaPayload(?MediaItemInterface $media): ?array
    {
        if (! $media instanceof MediaItemInterface) {
            return null;
        }

        return [
            'url' => $media->url(),
            'alt' => $media->metadata()['alt'] ?? null,
        ];
    }
}
