<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccess;

use Liberu\Cms\Core\Module\AbstractModule;

final class ContentAccessModule extends AbstractModule
{
    public function key(): string
    {
        return 'content-access';
    }

    public function name(): string
    {
        return 'Content Access';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
