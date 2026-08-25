<?php

declare(strict_types=1);

namespace Liberu\Cms\Seo;

use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Seo\SitemapRegistryInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

/**
 * Owns the SEO surface: it binds the sitemap registry before content modules
 * register (so their `app()->bound(...)` guard passes), then loads the public
 * sitemap/robots routes and the head-tag view components.
 */
final class CmsSeoServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new CmsSeoModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/seo.php', 'cms-seo');

        $this->app->singleton(SitemapRegistryInterface::class, SitemapRegistry::class);
        $this->app->singleton(SeoMetadataService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        $this->loadModuleRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadModuleViews(__DIR__.'/../resources/views', 'cms-seo');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/seo.php' => $this->app->configPath('cms-seo.php'),
            ], 'cms-seo-config');
        }
    }
}
