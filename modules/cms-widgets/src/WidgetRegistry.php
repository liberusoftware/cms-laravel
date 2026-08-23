<?php

declare(strict_types=1);

namespace Liberu\Cms\Widgets;

use Liberu\Cms\Contracts\Widget\WidgetArea;
use Liberu\Cms\Contracts\Widget\WidgetInterface;

/**
 * The catalogue of widgets. Modules register widgets; areas render the ones
 * assigned to them, in order.
 */
final class WidgetRegistry
{
    /**
     * @var array<string, WidgetInterface>
     */
    private array $widgets = [];

    public function register(WidgetInterface $widget): void
    {
        $this->widgets[$widget->key()] = $widget;
    }

    /**
     * @return array<string, WidgetInterface>
     */
    public function all(): array
    {
        return $this->widgets;
    }

    /**
     * Widgets in an area, ordered ascending.
     *
     * @return array<int, WidgetInterface>
     */
    public function forArea(WidgetArea $area): array
    {
        $matching = array_filter(
            $this->widgets,
            fn (WidgetInterface $widget): bool => $widget->area() === $area,
        );

        usort($matching, fn (WidgetInterface $a, WidgetInterface $b): int => $a->order() <=> $b->order());

        return $matching;
    }

    public function renderArea(WidgetArea $area): string
    {
        return implode('', array_map(
            fn (WidgetInterface $widget): string => $widget->render(),
            $this->forArea($area),
        ));
    }
}
