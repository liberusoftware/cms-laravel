<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Seo;

use Liberu\Cms\Contracts\Seo\SitemapUrl;
use Liberu\Cms\Contracts\Seo\SitemapUrlProviderInterface;
use Liberu\Cms\Pages\Contracts\PageRepositoryInterface;

/**
 * Contributes published Pages to the sitemap. The home page (slug "home") is
 * served at the site root, so it is listed as "/" rather than "/home".
 */
final readonly class PageSitemapProvider implements SitemapUrlProviderInterface
{
    public function __construct(private PageRepositoryInterface $pages) {}

    public function sitemapUrls(): iterable
    {
        foreach ($this->pages->published() as $page) {
            yield new SitemapUrl(
                loc: url($page->slug === 'home' ? '/' : '/'.$page->slug),
                lastModified: $page->publishedAt(),
                changeFrequency: 'weekly',
            );
        }
    }
}
