<?php

declare(strict_types=1);

namespace Liberu\Cms\Users;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * Identity & Access. Foundational: everything downstream authorizes against it,
 * so it cannot be disabled through the module manager.
 */
final class UsersModule extends AbstractModule
{
    public function key(): string
    {
        return 'users';
    }

    public function name(): string
    {
        return 'Users & Access';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    #[\Override]
    public function isFoundational(): bool
    {
        return true;
    }
}
