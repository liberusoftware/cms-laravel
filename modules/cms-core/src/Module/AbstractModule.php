<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Module;

use Liberu\Cms\Contracts\Module\ModuleInterface;

/**
 * Convenience base for module descriptors.
 *
 * A module ships a small subclass declaring its key, name, version, and
 * dependencies. Foundational status defaults to false, so ordinary modules
 * stay removable without any extra code.
 */
abstract class AbstractModule implements ModuleInterface
{
    public function dependencies(): array
    {
        return [];
    }

    public function isFoundational(): bool
    {
        return false;
    }
}
