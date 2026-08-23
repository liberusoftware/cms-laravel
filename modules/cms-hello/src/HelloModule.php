<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * Descriptor for the Hello proof-of-concept module.
 *
 * Deliberately non-foundational so the platform's "disable any module and the
 * app still boots" guarantee is exercised against it in CI.
 */
final class HelloModule extends AbstractModule
{
    public function key(): string
    {
        return 'hello';
    }

    public function name(): string
    {
        return 'Hello';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
