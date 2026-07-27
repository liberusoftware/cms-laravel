<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Admin;

/**
 * The catalogue of figures modules contribute to the admin dashboard overview.
 *
 * Modules register their stats when they boot; the Admin module reads the
 * catalogue and renders each one, evaluating its value closure at render time.
 *
 * @api This interface is part of the public extension API.
 */
interface AdminDashboardRegistryInterface
{
    public function registerStat(DashboardStat $stat): void;

    /**
     * Every registered stat, in registration order.
     *
     * @return array<int, DashboardStat>
     */
    public function stats(): array;
}
