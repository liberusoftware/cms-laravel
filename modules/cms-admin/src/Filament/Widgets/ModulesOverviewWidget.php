<?php

declare(strict_types=1);

namespace Liberu\Cms\Admin\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Liberu\Cms\Contracts\Module\ModuleManagerInterface;
use Liberu\Cms\Contracts\Module\ModuleRegistryInterface;

/**
 * Dashboard overview of the module system: how many modules are installed and
 * how many are currently enabled. Consumes only the core module contracts.
 */
class ModulesOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $registry = app(ModuleRegistryInterface::class);
        $manager = app(ModuleManagerInterface::class);

        $modules = $registry->all();
        $total = count($modules);

        $enabled = 0;

        foreach ($modules as $module) {
            if ($manager->isEnabled($module->key())) {
                $enabled++;
            }
        }

        return [
            Stat::make('Modules installed', (string) $total)
                ->descriptionIcon('heroicon-o-puzzle-piece'),
            Stat::make('Enabled', (string) $enabled)
                ->color('success'),
            Stat::make('Disabled', (string) ($total - $enabled))
                ->color($total - $enabled > 0 ? 'danger' : 'gray'),
        ];
    }
}
