<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLocking;

use Liberu\Cms\Core\Module\AbstractModule;

final class ContentLockingModule extends AbstractModule
{
    public function key(): string
    {
        return 'content-locking';
    }

    public function name(): string
    {
        return 'Content Locking';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
