<?php

declare(strict_types=1);

namespace Liberu\Cms\Widgets;

use Liberu\Cms\Core\Module\AbstractModule;

final class WidgetsModule extends AbstractModule
{
    public function key(): string
    {
        return 'widgets';
    }

    public function name(): string
    {
        return 'Widgets';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
