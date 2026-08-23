<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus;

use Liberu\Cms\Core\Module\AbstractModule;

final class MenusModule extends AbstractModule
{
    public function key(): string
    {
        return 'menus';
    }

    public function name(): string
    {
        return 'Menus';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
