<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Seo;

/**
 * A source of public URLs for the sitemap. Each content module implements this
 * for the content it exposes on the public site and registers it with the
 * sitemap registry, so the SEO module aggregates every module's URLs without
 * ever importing a module.
 */
interface SitemapUrlProviderInterface
{
    /**
     * The public URLs this provider contributes to the sitemap.
     *
     * @return iterable<int, SitemapUrl>
     */
    public function sitemapUrls(): iterable;
}
