<?php

declare(strict_types=1);

namespace Liberu\Cms\Seo\Http\Controllers;

use Illuminate\Http\Response;
use Liberu\Cms\Contracts\Seo\SitemapRegistryInterface;
use Liberu\Cms\Contracts\Seo\SitemapUrl;

/**
 * Renders sitemap.xml by aggregating the URLs every registered module provider
 * contributes. The public site is unauthenticated, so URLs are unscoped — the
 * same visibility the public web routes have.
 */
final readonly class SitemapController
{
    public function __construct(private SitemapRegistryInterface $registry) {}

    public function __invoke(): Response
    {
        $urls = [];

        foreach ($this->registry->providers() as $provider) {
            foreach ($provider->sitemapUrls() as $url) {
                if ($url instanceof SitemapUrl) {
                    $urls[] = $url;
                }
            }
        }

        return response()
            ->view('cms-seo::sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
