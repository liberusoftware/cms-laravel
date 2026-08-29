<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFramework;

use Liberu\Cms\Core\Module\AbstractModule;

final class MigrationFrameworkModule extends AbstractModule
{
    public function key(): string
    {
        return 'migration-framework';
    }

    public function name(): string
    {
        return 'Migration Framework';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
