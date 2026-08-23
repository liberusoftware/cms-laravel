<?php

declare(strict_types=1);

namespace Liberu\Cms\Admin\Filament\Support;

/**
 * A read-only projection of a module's state for the admin table. Assembled from
 * the registry and module manager so the Blade view never touches those services
 * directly.
 */
final readonly class ModuleView
{
    /**
     * @param  array<int, string>  $dependencies  Keys this module requires.
     * @param  array<int, string>  $dependents  Enabled keys that require this module.
     */
    public function __construct(
        public string $key,
        public string $name,
        public string $version,
        public bool $enabled,
        public bool $foundational,
        public array $dependencies,
        public array $dependents,
    ) {}

    /**
     * Whether an administrator may toggle this module. Foundational modules are
     * permanent, and an enabled module with enabled dependents cannot be
     * disabled without orphaning them.
     */
    public function isToggleable(): bool
    {
        if ($this->foundational) {
            return false;
        }

        if ($this->enabled && $this->dependents !== []) {
            return false;
        }

        return true;
    }

    public function lockReason(): ?string
    {
        if ($this->foundational) {
            return 'Foundational module — always enabled.';
        }

        if ($this->enabled && $this->dependents !== []) {
            return 'Required by: '.implode(', ', $this->dependents);
        }

        return null;
    }
}
