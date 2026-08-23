<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Module;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Module\ModuleManagerInterface;
use Liberu\Cms\Contracts\Module\ModuleRegistryInterface;

/**
 * Base service provider that gives every CMS module the same isolation
 * behaviour and turns it into a self-gating, removable unit.
 *
 * A module's provider is always discovered by Laravel. During boot it announces
 * its descriptor to the registry (so the dependency graph is complete even for
 * disabled modules) and then loads its functionality only when the module
 * manager reports it enabled. Disabling a module therefore leaves its provider
 * registered but inert: no migrations, routes, views, config, or listeners load.
 *
 * Subclasses implement module() and override registerModule()/bootModule().
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The module's descriptor.
     */
    abstract public function module(): ModuleInterface;

    /**
     * Container bindings for the module's own services.
     *
     * Runs unconditionally so contracts resolve even before boot; the bindings
     * are inert until the module's functionality is loaded.
     */
    protected function registerModule(): void {}

    /**
     * Functionality loaded only when the module is enabled: routes, views,
     * migrations, event listeners, and admin surfaces.
     */
    protected function bootModule(): void {}

    #[\Override]
    final public function register(): void
    {
        $this->registerModule();
    }

    final public function boot(): void
    {
        $this->app->make(ModuleRegistryInterface::class)->register($this->module());

        if (! $this->moduleIsEnabled()) {
            return;
        }

        $this->bootModule();
    }

    protected function moduleIsEnabled(): bool
    {
        return $this->app->make(ModuleManagerInterface::class)->isEnabled($this->module()->key());
    }

    protected function loadModuleMigrations(string $path): void
    {
        $this->loadMigrationsFrom($path);
    }

    protected function loadModuleRoutesFrom(string $path): void
    {
        if (file_exists($path)) {
            $this->loadRoutesFrom($path);
        }
    }

    protected function loadModuleViews(string $path, string $namespace): void
    {
        $this->loadViewsFrom($path, $namespace);
    }

    protected function loadModuleTranslations(string $path, string $namespace): void
    {
        $this->loadTranslationsFrom($path, $namespace);
    }

    protected function mergeModuleConfig(string $path, string $key): void
    {
        $this->mergeConfigFrom($path, $key);
    }
}
