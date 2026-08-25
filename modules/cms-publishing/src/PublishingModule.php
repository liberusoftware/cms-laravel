<?php

declare(strict_types=1);

namespace Liberu\Cms\Publishing;

use Liberu\Cms\Core\Module\AbstractModule;

final class PublishingModule extends AbstractModule
{
    public function key(): string
    {
        return 'publishing';
    }

    public function name(): string
    {
        return 'Publishing';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
