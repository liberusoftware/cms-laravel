<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwa;

use Liberu\Cms\Core\Module\AbstractModule;

final class OfflineAndPwaModule extends AbstractModule
{
    public function key(): string
    {
        return 'offline-and-pwa';
    }

    public function name(): string
    {
        return 'Offline and PWA';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
