<?php

declare(strict_types=1);

namespace Liberu\Cms\Widgets\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Liberu\Cms\Contracts\Widget\WidgetArea;
use Liberu\Cms\Widgets\WidgetRegistry;
use UnitEnum;

/**
 * Admin surface for the Widgets module: an overview of every registered widget
 * grouped by the area it renders in, in render order. Widgets are declared in
 * code and collected in a registry, so this is a read-only page rather than a
 * resource.
 */
class WidgetOverview extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected string $view = 'cms-widgets::filament.pages.widget-overview';

    protected static ?string $title = 'Widgets';

    protected static ?string $navigationLabel = 'Widgets';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'CMS';
    }

    /**
     * @return array<int, array{area: string, widgets: array<int, array{key: string, title: string, order: int}>}>
     */
    public function areas(): array
    {
        $registry = app(WidgetRegistry::class);

        $result = [];

        foreach (WidgetArea::cases() as $area) {
            $widgets = [];

            foreach ($registry->forArea($area) as $widget) {
                $widgets[] = [
                    'key' => $widget->key(),
                    'title' => $widget->title(),
                    'order' => $widget->order(),
                ];
            }

            $result[] = [
                'area' => ucfirst($area->value),
                'widgets' => $widgets,
            ];
        }

        return $result;
    }
}
