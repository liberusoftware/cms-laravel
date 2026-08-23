<?php

declare(strict_types=1);

namespace Liberu\Cms\Themes;

use Liberu\Cms\Contracts\Theme\ThemeInterface;

/**
 * Convenience base for theme descriptors; base themes need not declare a parent.
 */
abstract class AbstractTheme implements ThemeInterface
{
    public function parent(): ?string
    {
        return null;
    }
}
