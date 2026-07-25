<?php

declare(strict_types=1);

namespace Liberu\Cms\Seo;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * SEO. Provides the public sitemap.xml and robots.txt for the server-rendered
 * site, plus meta / OpenGraph / JSON-LD head tags. It consumes only contracts
 * and the core module system, so it can be enabled or removed on its own.
 */
final class CmsSeoModule extends AbstractModule
{
    public function key(): string
    {
        return 'seo';
    }

    public function name(): string
    {
        return 'SEO';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
