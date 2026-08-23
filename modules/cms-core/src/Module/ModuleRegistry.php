<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Module;

use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Module\ModuleRegistryInterface;

/**
 * In-memory catalogue of every module whose service provider has booted.
 *
 * Registration is idempotent and happens for enabled and disabled modules
 * alike, so the dependency graph the manager reasons over is always complete.
 */
final class ModuleRegistry implements ModuleRegistryInterface
{
    /**
     * @var array<string, ModuleInterface>
     */
    private array $modules = [];

    public function register(ModuleInterface $module): void
    {
        $this->modules[$module->key()] = $module;
    }

    public function has(string $key): bool
    {
        return isset($this->modules[$key]);
    }

    public function get(string $key): ?ModuleInterface
    {
        return $this->modules[$key] ?? null;
    }

    public function all(): array
    {
        return $this->modules;
    }
}
