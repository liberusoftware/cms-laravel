<?php

declare(strict_types=1);

namespace Liberu\Cms\Multisite;

use Liberu\Cms\Core\Module\AbstractModule;

final class MultisiteModule extends AbstractModule
{
    public function key(): string
    {
        return 'multisite';
    }

    public function name(): string
    {
        return 'Multisite';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
