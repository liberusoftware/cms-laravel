<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishing;

use Liberu\Cms\Core\Module\AbstractModule;

final class StaticPublishingModule extends AbstractModule
{
    public function key(): string
    {
        return 'static-publishing';
    }

    public function name(): string
    {
        return 'Static Publishing';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
