<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionManager;

use Liberu\Cms\Core\Module\AbstractModule;

final class ExtensionManagerModule extends AbstractModule
{
    public function key(): string
    {
        return 'extension-manager';
    }

    public function name(): string
    {
        return 'Extension Manager';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
