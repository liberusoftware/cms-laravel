<?php

declare(strict_types=1);

namespace Liberu\Cms\Revisions;

use Liberu\Cms\Core\Module\AbstractModule;

final class RevisionsModule extends AbstractModule
{
    public function key(): string
    {
        return 'revisions';
    }

    public function name(): string
    {
        return 'Revisions';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
