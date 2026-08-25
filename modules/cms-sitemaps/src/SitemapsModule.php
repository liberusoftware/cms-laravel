<?php

declare(strict_types=1);

namespace Liberu\Cms\Sitemaps;

use Liberu\Cms\Core\Module\AbstractModule;

final class SitemapsModule extends AbstractModule
{
    public function key(): string
    {
        return 'sitemaps';
    }

    public function name(): string
    {
        return 'Sitemaps';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
