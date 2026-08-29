<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystem;

use Liberu\Cms\Core\Module\AbstractModule;

final class FieldSystemModule extends AbstractModule
{
    public function key(): string
    {
        return 'field-system';
    }

    public function name(): string
    {
        return 'Field System';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
