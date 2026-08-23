<?php

declare(strict_types=1);

namespace Liberu\Cms\Themes;

/**
 * A concrete theme descriptor for registering themes without a bespoke class.
 */
final class Theme extends AbstractTheme
{
    public function __construct(
        private readonly string $key,
        private readonly string $name,
        private readonly string $viewsPath,
        private readonly ?string $parent = null,
    ) {}

    public function key(): string
    {
        return $this->key;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function parent(): ?string
    {
        return $this->parent;
    }

    public function viewsPath(): string
    {
        return $this->viewsPath;
    }
}
