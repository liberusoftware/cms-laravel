<?php

declare(strict_types=1);

namespace Liberu\Cms\Admin\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Resources\Resource;
use Liberu\Cms\Admin\Filament\Pages\ModuleManagement;
use Liberu\Cms\Admin\Filament\Widgets\ContentOverviewWidget;
use Liberu\Cms\Admin\Filament\Widgets\ModulesOverviewWidget;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

/**
 * Registers the CMS admin surfaces onto a Filament panel. A host opts in with
 * `->plugin(CmsAdminPlugin::make())`; removing that one line (or the package)
 * takes the whole admin surface with it, per the removable-module rule.
 *
 * Content modules contribute their own Filament resources through the resource
 * registry during the register phase; the plugin registers every contributed
 * resource so each installed module gets its admin surface. (Enable/disable of a
 * module governs its runtime functionality — routes, services, migrations —
 * through the module's self-gating provider, not this management surface.)
 */
final class CmsAdminPlugin implements Plugin
{
    public static function make(): static
    {
        return app(self::class);
    }

    public function getId(): string
    {
        return 'cms-admin';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            ModuleManagement::class,
        ]);

        $panel->widgets([
            ModulesOverviewWidget::class,
            ContentOverviewWidget::class,
        ]);

        // Modules populate the registry during the register phase, which
        // completes only after the panel is first built. Deferring the lookup
        // into a route closure — Filament runs these when it registers the
        // panel's routes, after every provider has registered — guarantees the
        // registry is fully populated before we read it.
        $panel->routes(function (Panel $panel): void {
            $panel->resources($this->registered(fn (AdminResourceRegistryInterface $registry): array => $registry->resources()));
            $panel->pages($this->registered(fn (AdminResourceRegistryInterface $registry): array => $registry->pages()));
        });
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * Flatten a registry catalogue (resources or pages) into a list of existing
     * class names.
     *
     * @param  callable(AdminResourceRegistryInterface): array<string, array<int, string>>  $catalogue
     * @return array<int, class-string>
     */
    private function registered(callable $catalogue): array
    {
        if (! app()->bound(AdminResourceRegistryInterface::class)) {
            return [];
        }

        $classes = [];

        foreach ($catalogue(app(AdminResourceRegistryInterface::class)) as $moduleKey => $moduleClasses) {
            foreach ($moduleClasses as $class) {
                if (class_exists($class)) {
                    if (is_subclass_of($class, Resource::class)) {
                        $class::navigationGroup($this->navigationGroup($moduleKey));
                    }

                    $classes[] = $class;
                }
            }
        }

        return $classes;
    }

    private function navigationGroup(string $moduleKey): string
    {
        $groups = [
            'content' => 'Content',
            'post' => 'Content',
            'page' => 'Content',
            'collection' => 'Content',
            'form' => 'Content',
            'taxonomy' => 'Structure',
            'category' => 'Structure',
            'tag' => 'Structure',
            'menu' => 'Structure',
            'navigation' => 'Structure',
            'core' => 'Structure',
            'field' => 'Structure',
            'asset' => 'Assets',
            'media' => 'Assets',
            'image' => 'Assets',
            'video' => 'Assets',
            'embed' => 'Assets',
            'experience' => 'Experience',
            'seo' => 'Experience',
            'redirect' => 'Experience',
            'recommend' => 'Experience',
            'personal' => 'Experience',
            'localiz' => 'Experience',
            'setting' => 'Settings',
            'api' => 'Administration',
            'user' => 'Administration',
            'audit' => 'Administration',
            'security' => 'Administration',
            'notification' => 'Administration',
            'operation' => 'Operations',
            'backup' => 'Operations',
            'cache' => 'Operations',
            'revision' => 'Operations',
        ];

        foreach ($groups as $needle => $group) {
            if (str_contains($moduleKey, $needle)) {
                return $group;
            }
        }

        return 'Content';
    }
}
