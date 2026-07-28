<?php

declare(strict_types=1);

namespace Liberu\Cms\Search;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Contracts\Health\HealthCheckRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Search\SearchIndexInterface;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Search\Health\SearchHealthCheck;
use Liberu\Cms\Search\Http\Controllers\SearchController;
use Liberu\Cms\Search\Index\DatabaseSearchIndex;
use Liberu\Cms\Search\Index\ScoutSearchIndex;

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
        $this->app->singleton(SearchIndexInterface::class, function (Application $app): SearchIndexInterface {
            $driver = config('cms-search.driver', 'database');

            return $driver === 'scout'
                ? $app->make(ScoutSearchIndex::class)
                : $app->make(DatabaseSearchIndex::class);
        });

        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint(
                'search',
                new ApiEndpoint('search', SearchController::class, 'index', 'search.index'),
            );
        }
    }

    protected function bootModule(): void
    {
        $this->registerHealthCheck();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/search.php' => $this->app->configPath('cms-search.php'),
            ], 'cms-search-config');
        }
    }

    /**
     * Contribute the search readiness probe — backed by the active driver's
     * `isReady()` — to the observability registry when it is present. Criticality
     * is owned by this module's own config (defaulting to degraded), so nothing
     * here reaches into the observability module.
     */
    private function registerHealthCheck(): void
    {
        if (! $this->app->bound(HealthCheckRegistryInterface::class)) {
            return;
        }

        $critical = (bool) $this->app->make(ConfigRepository::class)->get('cms-search.readiness.critical', false);

        $this->app->make(HealthCheckRegistryInterface::class)->register(new SearchHealthCheck(
            $this->app->make(SearchIndexInterface::class),
            $critical,
        ));
    }
}
