<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Module;

/**
 * Governs module lifecycle: enable/disable, dependency rules, and boot order.
 *
 * The manager is the single authority other systems consult to decide whether
 * a module's functionality should load. Modules never inspect each other's
 * state directly; they ask the manager.
 *
 * @api This interface is part of the public extension API.
 */
interface ModuleManagerInterface
{
    /**
     * Whether the module's functionality should currently load and run.
     *
     * Foundational modules always report true. Unknown modules report false.
     */
    public function isEnabled(string $key): bool;

    /**
     * Enable a module.
     *
     * @throws ModuleDependencyExceptionInterface
     *                                            when a required dependency is missing or disabled.
     */
    public function enable(string $key): void;

    /**
     * Disable a module.
     *
     * @throws ModuleDependencyExceptionInterface
     *                                            when the module is foundational, or an enabled module depends on it.
     */
    public function disable(string $key): void;

    /**
     * Keys of every enabled module in a safe boot order (dependencies first).
     *
     * @return array<int, string>
     */
    public function bootOrder(): array;

    /**
     * Keys of every module the given module depends on, transitively.
     *
     * @return array<int, string>
     */
    public function dependenciesOf(string $key): array;

    /**
     * Keys of enabled modules that depend on the given module, transitively.
     *
     * @return array<int, string>
     */
    public function dependentsOf(string $key): array;
}
