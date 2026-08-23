<?php

declare(strict_types=1);

namespace Liberu\Cms\Admin\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Liberu\Cms\Contracts\Admin\AdminDashboardRegistryInterface;

/**
 * Dashboard overview of the CMS content. Each figure is contributed by the
 * module that owns it via the dashboard registry, so this widget summarises the
 * whole installation without importing any content module.
 */
class ContentOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = [];

        foreach (app(AdminDashboardRegistryInterface::class)->stats() as $stat) {
            $rendered = Stat::make($stat->label, ($stat->value)());

            if ($stat->icon !== null) {
                $rendered = $rendered->descriptionIcon($stat->icon);
            }

            if ($stat->color !== null) {
                $rendered = $rendered->color($stat->color);
            }

            $stats[] = $rendered;
        }

        return $stats;
    }
}
