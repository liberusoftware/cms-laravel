<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections;

use Liberu\Cms\Core\Module\AbstractModule;

final class CollectionsModule extends AbstractModule
{
    public function key(): string
    {
        return 'collections';
    }

    public function name(): string
    {
        return 'Collections';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function dependencies(): array
    {
        return [];
    }
}
