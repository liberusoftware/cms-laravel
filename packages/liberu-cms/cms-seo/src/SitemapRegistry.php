<?php

declare(strict_types=1);

namespace Liberu\Cms\Seo;

use Liberu\Cms\Contracts\Seo\SitemapRegistryInterface;
use Liberu\Cms\Contracts\Seo\SitemapUrlProviderInterface;

/**
 * In-memory catalogue of module-contributed sitemap URL providers. Mirrors the
 * admin and API registries.
 */
final class SitemapRegistry implements SitemapRegistryInterface
{
    /**
     * @var array<int, SitemapUrlProviderInterface>
     */
    private array $providers = [];

    public function registerProvider(SitemapUrlProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    public function providers(): array
    {
        return $this->providers;
    }
}
