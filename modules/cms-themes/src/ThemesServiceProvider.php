<?php

declare(strict_types=1);

namespace Liberu\Cms\Themes;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Theme\ThemeInterface;
use Liberu\Cms\Contracts\Theme\ThemeManagerInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Themes\Filament\Pages\ThemeManagement;

final class ThemesServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ThemesModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/themes.php', 'cms-themes');

        $this->app->singleton(ThemeStateRepository::class, fn (): ThemeStateRepository => new ThemeStateRepository(
            $this->app->make(ConnectionResolverInterface::class),
        ));

        $this->app->singleton(ThemeManagerInterface::class, fn (): ThemeManager => new ThemeManager(
            $this->app->make(ThemeStateRepository::class),
            $this->app->make(EventBusInterface::class),
            $this->defaultThemeKey(),
        ));

        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerPage('themes', ThemeManagement::class);
        }
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        $this->loadModuleViews(__DIR__.'/../resources/views', 'cms-themes');

        $manager = $this->app->make(ThemeManagerInterface::class);
        $manager->register(new Theme($this->defaultThemeKey(), 'Default', $this->app->resourcePath('views')));

        $this->applyActiveThemeViews($manager);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/themes.php' => $this->app->configPath('cms-themes.php'),
            ], 'cms-themes-config');
        }
    }

    private function applyActiveThemeViews(ThemeManagerInterface $manager): void
    {
        if (! $manager->active() instanceof ThemeInterface) {
            return;
        }

        $finder = $this->app->make(ViewFactory::class)->getFinder();

        if (! $finder instanceof FileViewFinder) {
            return;
        }

        // Prepend nearest-last so the active (child) theme ends up first.
        foreach (array_reverse($manager->inheritanceChain()) as $theme) {
            if (is_dir($theme->viewsPath())) {
                $finder->prependLocation($theme->viewsPath());
            }
        }
    }

    private function defaultThemeKey(): string
    {
        $default = $this->app->make(ConfigRepository::class)->get('cms-themes.default');

        return is_string($default) ? $default : 'default';
    }
}
