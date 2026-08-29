<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionManager\Services;

use Liberu\Cms\Contracts\Module\ModuleManagerInterface;
use Liberu\Cms\Contracts\Module\ModuleRegistryInterface;

final readonly class ExtensionManagerService
{
    public function __construct(private ModuleRegistryInterface $registry, private ModuleManagerInterface $manager) {}

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $extensions = [];
        foreach ($this->registry->all() as $module) {
            $key = $module->key();
            $extensions[] = ['key' => $key, 'name' => $module->name(), 'version' => $module->version(), 'enabled' => $this->manager->isEnabled($key), 'foundational' => $module->isFoundational(), 'dependencies' => $module->dependencies(), 'dependents' => $this->manager->dependentsOf($key)];
        }
        usort($extensions, static fn (array $left, array $right): int => $left['name'] <=> $right['name']);

        return $extensions;
    }

    public function enable(string $key): void
    {
        $this->manager->enable($key);
    }

    public function disable(string $key): void
    {
        $this->manager->disable($key);
    }
}
