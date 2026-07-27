<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Module;

/**
 * Read model of every module known to the application.
 *
 * Each module's service provider registers its descriptor here during boot,
 * regardless of enabled state, so the dependency graph is always complete.
 *
 * @internal Kernel wiring; not part of the public extension API and may change without notice.
 */
interface ModuleRegistryInterface
{
    /**
     * Announce a module's existence to the registry.
     */
    public function register(ModuleInterface $module): void;

    /**
     * Whether a module with the given key has been registered.
     */
    public function has(string $key): bool;

    /**
     * Resolve a registered module descriptor by key, or null when unknown.
     */
    public function get(string $key): ?ModuleInterface;

    /**
     * All registered module descriptors, keyed by module key.
     *
     * @return array<string, ModuleInterface>
     */
    public function all(): array;
}
