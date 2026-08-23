<?php

declare(strict_types=1);

namespace Liberu\Cms\Admin;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * Admin. Provides the Filament panel surfaces that let administrators operate
 * the CMS. It consumes only the core module system and the access contracts, so
 * it can be enabled or removed without dragging any content module along.
 */
final class AdminModule extends AbstractModule
{
    public function key(): string
    {
        return 'admin';
    }

    public function name(): string
    {
        return 'Admin';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
