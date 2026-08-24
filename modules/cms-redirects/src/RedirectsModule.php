<?php

declare(strict_types=1);

namespace Liberu\Cms\Redirects;

use Liberu\Cms\Core\Module\AbstractModule;

final class RedirectsModule extends AbstractModule
{
    public function key(): string
    {
        return 'redirects';
    }

    public function name(): string
    {
        return 'Redirects';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
