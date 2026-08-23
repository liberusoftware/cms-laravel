<?php

declare(strict_types=1);

namespace Liberu\Cms\Widgets\Widgets;

use Liberu\Cms\Contracts\Widget\WidgetArea;
use Liberu\Cms\Contracts\Widget\WidgetInterface;

/**
 * Renders a list of social links. Labels and URLs are escaped.
 */
final readonly class SocialLinksWidget implements WidgetInterface
{
    /**
     * @param  array<string, string>  $links  label => url
     */
    public function __construct(private array $links = []) {}

    public function key(): string
    {
        return 'social-links';
    }

    public function title(): string
    {
        return 'Social Links';
    }

    public function area(): WidgetArea
    {
        return WidgetArea::Footer;
    }

    public function order(): int
    {
        return 0;
    }

    public function render(): string
    {
        $items = '';

        foreach ($this->links as $label => $url) {
            $items .= '<li><a href="'.htmlspecialchars($url, ENT_QUOTES).'">'.htmlspecialchars($label, ENT_QUOTES).'</a></li>';
        }

        return '<ul class="cms-widget cms-widget-social">'.$items.'</ul>';
    }
}
