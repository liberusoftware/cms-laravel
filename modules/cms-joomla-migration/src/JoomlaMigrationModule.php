<?php

declare(strict_types=1);

namespace Liberu\Cms\JoomlaMigration;

use Liberu\Cms\Core\Module\AbstractModule;

final class JoomlaMigrationModule extends AbstractModule
{
    public function key(): string
    {
        return 'joomla-migration';
    }

    public function name(): string
    {
        return 'Joomla Migration';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
