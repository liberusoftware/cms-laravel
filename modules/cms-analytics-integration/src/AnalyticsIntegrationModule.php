<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegration;

use Liberu\Cms\Core\Module\AbstractModule;

final class AnalyticsIntegrationModule extends AbstractModule
{
    public function key(): string
    {
        return 'analytics-integration';
    }

    public function name(): string
    {
        return 'Analytics Integration';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
