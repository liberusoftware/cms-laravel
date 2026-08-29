<?php

declare(strict_types=1);

namespace Liberu\Cms\Copilot;

use Liberu\Cms\Core\Module\AbstractModule;

final class CopilotModule extends AbstractModule
{
    public function key(): string
    {
        return 'cms-copilot';
    }

    public function name(): string
    {
        return 'CMS Copilot';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
