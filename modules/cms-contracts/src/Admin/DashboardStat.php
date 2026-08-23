<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Admin;

use Closure;

/**
 * A single figure a module contributes to the admin dashboard overview.
 *
 * The value is a closure evaluated at render time (never at registration), so a
 * module can hand the Admin module a live count of its own models without the
 * Admin module ever importing them.
 *
 * @api This class is part of the public extension API.
 */
final readonly class DashboardStat
{
    /**
     * @param  string  $label  Human label, e.g. "Pages".
     * @param  Closure(): (int|string)  $value  Returns the current figure at render time.
     * @param  string|null  $icon  Optional heroicon name, e.g. "heroicon-o-document-text".
     * @param  string|null  $color  Optional Filament colour, e.g. "primary", "success".
     */
    public function __construct(
        public string $label,
        public Closure $value,
        public ?string $icon = null,
        public ?string $color = null,
    ) {}
}
