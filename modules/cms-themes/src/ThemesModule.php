<?php

declare(strict_types=1);

namespace Liberu\Cms\Themes;

use Liberu\Cms\Core\Module\AbstractModule;

final class ThemesModule extends AbstractModule
{
    public function key(): string
    {
        return 'themes';
    }

    public function name(): string
    {
        return 'Themes';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
