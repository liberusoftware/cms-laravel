<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplace;

use Liberu\Cms\Core\Module\AbstractModule;

final class ThemeMarketplaceModule extends AbstractModule
{
    public function key(): string
    {
        return 'theme-marketplace';
    }

    public function name(): string
    {
        return 'Theme Marketplace';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
