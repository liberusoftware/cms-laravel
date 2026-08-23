<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Events\Content;

use Liberu\Cms\Contracts\Events\CmsEvent;

/**
 * Emitted when a content item enters the Published state. SEO, search, caching,
 * and notification modules listen for this.
 *
 * @api This class is part of the public extension API.
 */
final readonly class ContentPublished implements CmsEvent
{
    public function __construct(
        public string $contentType,
        public int|string $contentId,
    ) {}

    public function name(): string
    {
        return 'content.published';
    }
}
