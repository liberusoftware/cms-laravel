<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Module;

/**
 * Describes a single CMS module to the core registry.
 *
 * A module ships exactly one implementation of this contract (its descriptor).
 * The descriptor carries only metadata; it never performs work. Discovery,
 * enable/disable, and boot ordering are driven from the values returned here.
 *
 * @api This interface is part of the public extension API.
 */
interface ModuleInterface
{
    /**
     * Stable machine key, e.g. "hello", "pages", "media".
     *
     * Used as the primary identifier across the registry, state storage,
     * dependency graph, and config. Must be unique and never change once shipped.
     */
    public function key(): string;

    /**
     * Human-readable module name for admin surfaces.
     */
    public function name(): string;

    /**
     * Semantic version of the module, independent of every other module.
     */
    public function version(): string;

    /**
     * Keys of the modules this module requires to be enabled before it may boot.
     *
     * @return array<int, string>
     */
    public function dependencies(): array;

    /**
     * Whether this module is part of the non-removable foundation.
     *
     * Foundational modules cannot be disabled through the module manager.
     */
    public function isFoundational(): bool;
}
