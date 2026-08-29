<?php

declare(strict_types=1);

namespace Liberu\Cms\LayoutBuilder;

use Liberu\Cms\Core\Module\AbstractModule;

final class LayoutBuilderModule extends AbstractModule
{
    public function key(): string
    {
        return 'layout-builder';
    }

    public function name(): string
    {
        return 'Layout Builder';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
