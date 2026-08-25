<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrity;

use Liberu\Cms\Core\Module\AbstractModule;

final class ContentIntegrityModule extends AbstractModule
{
    public function key(): string
    {
        return 'content-integrity';
    }

    public function name(): string
    {
        return 'Content Integrity';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
