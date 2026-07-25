<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Seo;

/**
 * The catalogue of sitemap URL providers modules contribute. A module announces
 * its provider during the register phase; the SEO module reads the catalogue
 * when it renders sitemap.xml — so the sitemap tracks the installed modules
 * without the SEO module importing one. Mirrors the admin and API registries.
 */
interface SitemapRegistryInterface
{
    public function registerProvider(SitemapUrlProviderInterface $provider): void;

    /**
     * @return array<int, SitemapUrlProviderInterface>
     */
    public function providers(): array;
}
