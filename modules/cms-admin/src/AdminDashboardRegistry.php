<?php

declare(strict_types=1);

namespace Liberu\Cms\Admin;

use Liberu\Cms\Contracts\Admin\AdminDashboardRegistryInterface;
use Liberu\Cms\Contracts\Admin\DashboardStat;

/**
 * In-memory catalogue of dashboard stats contributed by modules.
 */
final class AdminDashboardRegistry implements AdminDashboardRegistryInterface
{
    /**
     * @var array<int, DashboardStat>
     */
    private array $stats = [];

    public function registerStat(DashboardStat $stat): void
    {
        $this->stats[] = $stat;
    }

    public function stats(): array
    {
        return $this->stats;
    }
}
