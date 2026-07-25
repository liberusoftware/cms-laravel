<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Seo;

use DateTimeInterface;

/**
 * A single entry in the sitemap: an absolute URL and its optional crawl hints.
 * Content modules build these for their public URLs and hand them to the sitemap
 * registry; the SEO module renders them into sitemap.xml.
 */
final class SitemapUrl
{
    public function __construct(
        public string $loc,
        public ?DateTimeInterface $lastModified = null,
        public ?string $changeFrequency = null,
        public ?float $priority = null,
    ) {}
}
