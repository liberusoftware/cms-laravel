<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegration;

use Liberu\Cms\Core\Module\AbstractModule;

final class ThemeIntegrationModule extends AbstractModule
{
    public function key(): string
    {
        return 'theme-integration';
    }

    public function name(): string
    {
        return 'Theme Integration';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
