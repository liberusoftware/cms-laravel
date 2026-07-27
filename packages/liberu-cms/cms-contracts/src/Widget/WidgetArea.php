<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Widget;

/**
 * Where a widget renders (Part B §12).
 *
 * @api This enum is part of the public extension API.
 */
enum WidgetArea: string
{
    case Sidebar = 'sidebar';
    case Dashboard = 'dashboard';
    case Footer = 'footer';
}
