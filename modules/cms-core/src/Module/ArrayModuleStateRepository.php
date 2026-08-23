<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Module;

use Liberu\Cms\Contracts\Module\ModuleStateRepositoryInterface;

/**
 * In-memory module state, with no persistence.
 *
 * Useful for tests and for embedded hosts that prefer to drive module state
 * from their own configuration rather than the cms_modules table.
 */
final class ArrayModuleStateRepository implements ModuleStateRepositoryInterface
{
    /**
     * @param  array<string, bool>  $state
     */
    public function __construct(private array $state = []) {}

    public function isEnabled(string $key, bool $default = true): bool
    {
        return $this->state[$key] ?? $default;
    }

    public function setEnabled(string $key, bool $enabled): void
    {
        $this->state[$key] = $enabled;
    }

    public function forget(string $key): void
    {
        unset($this->state[$key]);
    }
}
