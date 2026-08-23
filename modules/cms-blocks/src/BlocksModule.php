<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks;

use Liberu\Cms\Core\Module\AbstractModule;

final class BlocksModule extends AbstractModule
{
    public function key(): string
    {
        return 'blocks';
    }

    public function name(): string
    {
        return 'Blocks';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
