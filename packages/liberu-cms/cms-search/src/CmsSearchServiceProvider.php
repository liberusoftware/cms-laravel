<?php

declare(strict_types=1);

namespace Liberu\Cms\Search;

use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Search\Http\Controllers\SearchController;

/**
 * Owns the search surface: it binds the search registry so content modules can
 * contribute sources, and registers the `/api/v1/search` endpoint into the API
 * resource registry (cms-api loads first, so the binding is available). The
 * endpoint therefore inherits the Delivery API's auth, tenant context, and rate
 * limiting.
 */
final class CmsSearchServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new CmsSearchModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/search.php', 'cms-search');

        $this->app->singleton(SearchRegistryInterface::class, SearchRegistry::class);

        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint(
                'search',
                new ApiEndpoint('search', SearchController::class, 'index', 'search.index'),
            );
        }
    }

    protected function bootModule(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/search.php' => $this->app->configPath('cms-search.php'),
            ], 'cms-search-config');
        }
    }
}
