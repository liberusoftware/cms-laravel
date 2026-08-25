<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgets;

use Liberu\Cms\Core\Module\AbstractModule;

final class RegionsAndWidgetsModule extends AbstractModule
{
    public function key(): string
    {
        return 'regions-and-widgets';
    }

    public function name(): string
    {
        return 'Regions and Widgets';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
