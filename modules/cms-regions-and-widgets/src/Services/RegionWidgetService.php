<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgets\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\RegionsAndWidgets\Models\Region;
use Liberu\Cms\RegionsAndWidgets\Models\Widget;
use Liberu\Cms\RegionsAndWidgets\Models\WidgetPlacement;

final class RegionWidgetService
{
    public function region(string $key, ?int $teamId = null): Region
    {
        return Region::query()->where('key', Str::slug($key))->where('team_id', $teamId)->firstOrFail();
    }

    public function createRegion(string $key, string $label, ?string $theme = null, ?int $teamId = null): Region
    {
        if (trim($key) === '' || trim($label) === '') {
            throw ValidationException::withMessages(['label' => 'Region key and label are required.']);
        }

        return Region::query()->create(['key' => Str::slug($key), 'label' => $label, 'theme' => $theme, 'team_id' => $teamId]);
    }

    public function createWidget(string $key, string $type, array $configuration = [], ?string $title = null, ?int $teamId = null): Widget
    {
        if (trim($key) === '' || trim($type) === '') {
            throw ValidationException::withMessages(['type' => 'Widget key and type are required.']);
        }

        return Widget::query()->create(['key' => Str::slug($key), 'type' => $type, 'title' => $title, 'configuration' => $configuration, 'team_id' => $teamId]);
    }

    public function place(Region $region, Widget $widget, int $position = 0, array $visibility = [], ?string $startsAt = null, ?string $endsAt = null): WidgetPlacement
    {
        if ($startsAt !== null && $endsAt !== null && $startsAt >= $endsAt) {
            throw ValidationException::withMessages(['ends_at' => 'The placement end must be after its start.']);
        }
        $placement = WidgetPlacement::query()->updateOrCreate(['region_id' => $region->getKey(), 'widget_id' => $widget->getKey()], ['position' => max(0, $position), 'visibility' => $visibility, 'starts_at' => $startsAt, 'ends_at' => $endsAt, 'active' => true]);
        Cache::tags(['cms-regions-and-widgets', 'cms-region-'.$region->getKey()])->flush();

        return $placement;
    }

    /** @return array<int, array<string, mixed>> */
    public function render(string $regionKey, array $context = [], ?int $teamId = null): array
    {
        $region = $this->region($regionKey, $teamId);
        now();

        return Cache::tags(['cms-regions-and-widgets', 'cms-region-'.$region->getKey()])->remember('cms-region-render:'.$region->getKey().':'.sha1((string) json_encode($context)), 300, fn (): array => WidgetPlacement::query()->with('widget')->where('region_id', $region->getKey())->where('active', true)->orderBy('position')->get()->filter(fn (WidgetPlacement $placement): bool => $this->visible($placement, $context))->map(fn (WidgetPlacement $placement): array => ['key' => $placement->widget->key, 'type' => $placement->widget->type, 'title' => $placement->widget->title, 'configuration' => $placement->widget->configuration, 'position' => $placement->position])->values()->all());
    }

    private function visible(WidgetPlacement $placement, array $context): bool
    {
        if (! $placement->widget->active || ($placement->starts_at !== null && $placement->starts_at->isFuture()) || ($placement->ends_at !== null && $placement->ends_at->isPast())) {
            return false;
        }
        foreach ($placement->visibility ?? [] as $key => $expected) {
            if (($context[$key] ?? null) !== $expected) {
                return false;
            }
        }

        return true;
    }
}
