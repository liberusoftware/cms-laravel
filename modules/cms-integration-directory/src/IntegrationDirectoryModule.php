<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectory;

use Liberu\Cms\Core\Module\AbstractModule;

final class IntegrationDirectoryModule extends AbstractModule
{
    public function key(): string
    {
        return 'integration-directory';
    }

    public function name(): string
    {
        return 'Integration Directory';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
