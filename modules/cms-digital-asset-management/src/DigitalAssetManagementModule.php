<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagement;

use Liberu\Cms\Core\Module\AbstractModule;

final class DigitalAssetManagementModule extends AbstractModule
{
    public function key(): string
    {
        return 'digital-asset-management';
    }

    public function name(): string
    {
        return 'Digital Asset Management';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
