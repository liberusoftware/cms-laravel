<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperations;

use Liberu\Cms\Core\Module\AbstractModule;

final class SecurityOperationsModule extends AbstractModule
{
    public function key(): string
    {
        return 'security-operations';
    }

    public function name(): string
    {
        return 'Security Operations';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
