<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace;

use Liberu\Cms\Core\Module\AbstractModule;

final class ExtensionMarketplaceModule extends AbstractModule
{
    public function key(): string
    {
        return 'extension-marketplace';
    }

    public function name(): string
    {
        return 'Extension Marketplace';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
