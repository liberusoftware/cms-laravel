<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModes;

use Liberu\Cms\Core\Module\AbstractModule;

final class DisplayModesModule extends AbstractModule
{
    public function key(): string
    {
        return 'display-modes';
    }

    public function name(): string
    {
        return 'Display Modes';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
