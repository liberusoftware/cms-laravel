<?php

declare(strict_types=1);

namespace Liberu\Cms\LayoutBuilder\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\LayoutBuilder\Models\Layout;

final class LayoutBuilderService
{
    public function create(array $attributes): Layout
    {
        $this->validate($attributes);

        return DB::transaction(fn (): Layout => Layout::query()->create([...$attributes, 'definition' => $attributes['definition'], 'status' => $attributes['status'] ?? 'draft']));
    }

    public function update(Layout $layout, array $attributes): Layout
    {
        $this->validate([...$layout->only(['target_type', 'target_id', 'definition']), ...$attributes]);
        $layout->fill($attributes)->save();

        return $layout->refresh();
    }

    public function publish(Layout $layout): Layout
    {
        $this->validate(['definition' => $layout->definition, 'target_type' => $layout->target_type, 'target_id' => $layout->target_id]);
        $layout->update(['status' => 'published', 'published_at' => now()]);

        return $layout->refresh();
    }

    public function resolve(string $targetType, int|string $targetId, ?string $slug = null): ?Layout
    {
        return Layout::query()->published()->where('target_type', $targetType)->where('target_id', (string) $targetId)->when($slug, fn ($query) => $query->where('slug', $slug))->latest('published_at')->first();
    }

    private function validate(array $attributes): void
    {
        if (preg_match('/\\A[a-zA-Z][a-zA-Z0-9_.-]{0,79}\\z/', (string) ($attributes['target_type'] ?? '')) !== 1 || trim((string) ($attributes['target_id'] ?? '')) === '') {
            throw ValidationException::withMessages(['target' => 'A valid layout target is required.']);
        }
        if (! is_array($attributes['definition'] ?? null)) {
            throw ValidationException::withMessages(['definition' => 'The layout definition must be an object.']);
        }
        $regions = config('layout-builder.regions', []);
        $components = config('layout-builder.components', []);
        foreach ($attributes['definition']['regions'] ?? [] as $region => $items) {
            if (! is_string($region) || ! in_array($region, $regions, true) || ! is_array($items)) {
                throw ValidationException::withMessages(['definition' => 'Layouts may only use configured regions.']);
            }
            foreach ($items as $item) {
                if (! is_array($item) || ! is_string($item['component'] ?? null) || ! in_array($item['component'], $components, true) || ! is_array($item['settings'] ?? [])) {
                    throw ValidationException::withMessages(['definition' => 'Each layout item must use a configured component and settings object.']);
                }
            }
        }
    }
}
