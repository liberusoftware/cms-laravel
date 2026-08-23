<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Exceptions;

use Liberu\Cms\Contracts\Module\ModuleDependencyExceptionInterface;
use RuntimeException;

final class ModuleDependencyException extends RuntimeException implements ModuleDependencyExceptionInterface
{
    public static function missingDependency(string $module, string $dependency): self
    {
        return new self("Cannot enable module [{$module}]: required module [{$dependency}] is not registered.");
    }

    public static function disabledDependency(string $module, string $dependency): self
    {
        return new self("Cannot enable module [{$module}]: required module [{$dependency}] is disabled.");
    }

    public static function hasEnabledDependents(string $module, string $dependent): self
    {
        return new self("Cannot disable module [{$module}]: enabled module [{$dependent}] depends on it.");
    }

    public static function foundational(string $module): self
    {
        return new self("Cannot disable module [{$module}]: it is foundational and always enabled.");
    }

    public static function unknown(string $module): self
    {
        return new self("Unknown module [{$module}]: it is not registered.");
    }

    public static function cycle(string $module): self
    {
        return new self("Dependency cycle detected involving module [{$module}].");
    }
}
