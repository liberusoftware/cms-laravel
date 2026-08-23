<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Module;

/**
 * Persists the enabled/disabled decision for each toggleable module.
 *
 * Implementations must degrade gracefully: when backing storage is not yet
 * available (e.g. before migrations run), reads fall back to the default so
 * the application still boots.
 *
 * @internal Kernel wiring; not part of the public extension API and may change without notice.
 */
interface ModuleStateRepositoryInterface
{
    /**
     * Whether the module is enabled, using $default when no record exists.
     */
    public function isEnabled(string $key, bool $default = true): bool;

    /**
     * Persist an enabled/disabled decision for the module.
     */
    public function setEnabled(string $key, bool $enabled): void;

    /**
     * Drop any stored decision, reverting the module to its default state.
     */
    public function forget(string $key): void;
}
