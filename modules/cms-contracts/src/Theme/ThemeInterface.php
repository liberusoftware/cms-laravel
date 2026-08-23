<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Theme;

/**
 * A registered theme: a named set of Blade views, optionally inheriting from a
 * parent theme so it need only override the views it changes.
 *
 * @api This interface is part of the public extension API.
 */
interface ThemeInterface
{
    public function key(): string;

    public function name(): string;

    /**
     * The key of the theme this one inherits from, or null for a base theme.
     */
    public function parent(): ?string;

    /**
     * Absolute path to the theme's Blade views directory.
     */
    public function viewsPath(): string;
}
