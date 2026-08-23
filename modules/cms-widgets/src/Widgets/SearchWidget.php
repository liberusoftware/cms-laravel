<?php

declare(strict_types=1);

namespace Liberu\Cms\Widgets\Widgets;

use Liberu\Cms\Contracts\Widget\WidgetArea;
use Liberu\Cms\Contracts\Widget\WidgetInterface;

final class SearchWidget implements WidgetInterface
{
    public function key(): string
    {
        return 'search';
    }

    public function title(): string
    {
        return 'Search';
    }

    public function area(): WidgetArea
    {
        return WidgetArea::Sidebar;
    }

    public function order(): int
    {
        return 0;
    }

    public function render(): string
    {
        return '<form class="cms-widget cms-widget-search" role="search" action="/search" method="get">'
            .'<input type="search" name="q" placeholder="Search…">'
            .'<button type="submit">Search</button>'
            .'</form>';
    }
}
