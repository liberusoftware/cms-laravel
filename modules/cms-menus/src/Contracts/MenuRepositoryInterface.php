<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Contracts;

use Liberu\Cms\Menus\Models\Menu;

/**
 * Module-internal read boundary for menus.
 */
interface MenuRepositoryInterface
{
    public function find(int $id): ?Menu;

    /**
     * The menu assigned to a navigation location (header, footer, sidebar, …).
     */
    public function forLocation(string $location): ?Menu;
}
