<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagement;

use Liberu\Cms\Core\Module\AbstractModule;

final class ConfigurationManagementModule extends AbstractModule
{
    public function key(): string
    {
        return 'configuration-management';
    }

    public function name(): string
    {
        return 'Configuration Management';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
