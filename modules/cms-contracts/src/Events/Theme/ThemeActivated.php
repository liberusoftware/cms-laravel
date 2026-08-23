<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Events\Theme;

use Liberu\Cms\Contracts\Events\CmsEvent;

/**
 * Emitted when the active theme changes. Caches and asset pipelines listen so
 * they can flush and rebuild.
 *
 * @api This class is part of the public extension API.
 */
final readonly class ThemeActivated implements CmsEvent
{
    public function __construct(
        public string $themeKey,
        public ?string $previousThemeKey = null,
    ) {}

    public function name(): string
    {
        return 'theme.activated';
    }
}
