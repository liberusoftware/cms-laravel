<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactory;

use Liberu\Cms\Core\Module\AbstractModule;

final class SiteFactoryModule extends AbstractModule
{
    public function key(): string
    {
        return 'site-factory';
    }

    public function name(): string
    {
        return 'Site Factory';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
