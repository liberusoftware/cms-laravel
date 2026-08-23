<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Module;

use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Module\ModuleManagerInterface;
use Liberu\Cms\Contracts\Module\ModuleRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleStateRepositoryInterface;
use Liberu\Cms\Core\Exceptions\ModuleDependencyException;

/**
 * The single authority on module lifecycle.
 *
 * Enable/disable decisions are validated against the dependency graph before
 * they are persisted, and boot order is derived by topological sort so a module
 * never boots ahead of something it depends on.
 */
final readonly class ModuleManager implements ModuleManagerInterface
{
    /**
     * @param  array<int, string>  $forcedDisabled  Module keys the host has statically disabled.
     */
    public function __construct(
        private ModuleRegistryInterface $registry,
        private ModuleStateRepositoryInterface $state,
        private bool $enabledByDefault = true,
        private array $forcedDisabled = [],
    ) {}

    public function isEnabled(string $key): bool
    {
        $module = $this->registry->get($key);

        if (! $module instanceof ModuleInterface) {
            return false;
        }

        if ($module->isFoundational()) {
            return true;
        }

        if (in_array($key, $this->forcedDisabled, true)) {
            return false;
        }

        return $this->state->isEnabled($key, $this->enabledByDefault);
    }

    public function enable(string $key): void
    {
        $module = $this->registry->get($key)
            ?? throw ModuleDependencyException::unknown($key);

        foreach ($module->dependencies() as $dependency) {
            if (! $this->registry->has($dependency)) {
                throw ModuleDependencyException::missingDependency($key, $dependency);
            }

            if (! $this->isEnabled($dependency)) {
                throw ModuleDependencyException::disabledDependency($key, $dependency);
            }
        }

        $this->state->setEnabled($key, true);
    }

    public function disable(string $key): void
    {
        $module = $this->registry->get($key)
            ?? throw ModuleDependencyException::unknown($key);

        if ($module->isFoundational()) {
            throw ModuleDependencyException::foundational($key);
        }

        foreach ($this->registry->all() as $candidate) {
            if ($candidate->key() === $key) {
                continue;
            }

            if (in_array($key, $candidate->dependencies(), true) && $this->isEnabled($candidate->key())) {
                throw ModuleDependencyException::hasEnabledDependents($key, $candidate->key());
            }
        }

        $this->state->setEnabled($key, false);
    }

    public function bootOrder(): array
    {
        $order = [];
        $visiting = [];

        foreach ($this->registry->all() as $module) {
            if ($this->isEnabled($module->key())) {
                $this->visit($module->key(), $order, $visiting);
            }
        }

        return array_values($order);
    }

    public function dependenciesOf(string $key): array
    {
        $resolved = [];
        $this->collectDependencies($key, $resolved);

        return array_keys($resolved);
    }

    public function dependentsOf(string $key): array
    {
        $dependents = [];

        foreach ($this->registry->all() as $candidate) {
            $candidateKey = $candidate->key();

            if ($candidateKey === $key || ! $this->isEnabled($candidateKey)) {
                continue;
            }

            if (in_array($key, $this->dependenciesOf($candidateKey), true)) {
                $dependents[$candidateKey] = true;
            }
        }

        return array_keys($dependents);
    }

    /**
     * Depth-first topological visit that lists dependencies before dependents
     * and rejects cycles.
     *
     * @param  array<string, string>  $order
     * @param  array<string, bool>  $visiting
     */
    private function visit(string $key, array &$order, array &$visiting): void
    {
        if (isset($order[$key])) {
            return;
        }

        if (isset($visiting[$key])) {
            throw ModuleDependencyException::cycle($key);
        }

        $visiting[$key] = true;

        $module = $this->registry->get($key);

        if ($module instanceof ModuleInterface) {
            foreach ($module->dependencies() as $dependency) {
                if ($this->isEnabled($dependency)) {
                    $this->visit($dependency, $order, $visiting);
                }
            }
        }

        unset($visiting[$key]);

        $order[$key] = $key;
    }

    /**
     * @param  array<string, bool>  $resolved
     */
    private function collectDependencies(string $key, array &$resolved): void
    {
        $module = $this->registry->get($key);

        if (! $module instanceof ModuleInterface) {
            return;
        }

        foreach ($module->dependencies() as $dependency) {
            if (isset($resolved[$dependency])) {
                continue;
            }

            $resolved[$dependency] = true;
            $this->collectDependencies($dependency, $resolved);
        }
    }
}
