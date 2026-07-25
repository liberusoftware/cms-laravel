<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages;

use Liberu\Cms\Contracts\Admin\AdminDashboardRegistryInterface;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Admin\DashboardStat;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;
use Liberu\Cms\Contracts\Seo\SitemapRegistryInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Pages\Contracts\PageRepositoryInterface;
use Liberu\Cms\Pages\Filament\PageResource;
use Liberu\Cms\Pages\Http\Controllers\PageApiController;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Pages\Repositories\PageRepository;
use Liberu\Cms\Pages\Search\PageSearchSource;
use Liberu\Cms\Pages\Seo\PageSitemapProvider;

final class PagesServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new PagesModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/pages.php', 'cms-pages');

        $this->app->singleton(PageRepositoryInterface::class, PageRepository::class);

        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('pages', PageResource::class);
        }

        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('pages', new ApiEndpoint('pages', PageApiController::class, 'index', 'pages.index'));
            $registry->registerEndpoint('pages', new ApiEndpoint('pages/{slug}', PageApiController::class, 'show', 'pages.show'));
        }
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');

        if ($this->app->bound(AdminDashboardRegistryInterface::class)) {
            $this->app->make(AdminDashboardRegistryInterface::class)->registerStat(
                new DashboardStat('Pages', fn (): int => Page::count(), 'heroicon-o-document-text', 'primary'),
            );
        }

        if ($this->app->bound(SitemapRegistryInterface::class)) {
            $this->app->make(SitemapRegistryInterface::class)
                ->registerProvider($this->app->make(PageSitemapProvider::class));
        }

        if ($this->app->bound(SearchRegistryInterface::class)) {
            $this->app->make(SearchRegistryInterface::class)
                ->registerSource($this->app->make(PageSearchSource::class));
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/pages.php' => $this->app->configPath('cms-pages.php'),
            ], 'cms-pages-config');
        }
    }
}
