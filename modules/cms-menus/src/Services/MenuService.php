<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\Menus\Models\MenuItem;

/** Authoritative mutation boundary for the Navigation capability. */
final class MenuService
{
    /** @param array<string, mixed> $attributes */
    public function createMenu(array $attributes): Menu
    {
        $this->validateMenu($attributes);

        return Menu::create($attributes + ['variant' => 'default']);
    }

    /** @param array<string, mixed> $attributes */
    public function updateMenu(Menu $menu, array $attributes): Menu
    {
        $this->validateMenu($attributes, $menu);
        $menu->update($attributes);

        return $menu->refresh();
    }

    /** @param array<string, mixed> $attributes */
    public function saveItem(Menu $menu, array $attributes, ?MenuItem $item = null): MenuItem
    {
        $this->validateItem($menu, $attributes, $item);

        return DB::transaction(function () use ($menu, $attributes, $item): MenuItem {
            if ($item instanceof MenuItem) {
                $item->update($attributes);

                return $item->refresh();
            }

            return $menu->items()->create($attributes + ['team_id' => $menu->team_id]);
        });
    }

    public function deleteItem(MenuItem $item): void
    {
        $item->delete();
    }

    /** @param array<string, mixed> $attributes */
    private function validateMenu(array $attributes, ?Menu $menu = null): void
    {
        /** @var list<string> $fields */
        $fields = ['name', 'location', 'variant'];
        foreach ($fields as $field) {
            if (array_key_exists($field, $attributes) && $this->text($attributes[$field]) === '') {
                throw ValidationException::withMessages([$field => 'This field cannot be blank.']);
            }
        }

        if (! $menu instanceof Menu && (! isset($attributes['name'], $attributes['location']))) {
            throw ValidationException::withMessages(['menu' => 'A menu name and location are required.']);
        }
    }

    /** @param array<string, mixed> $attributes */
    private function validateItem(Menu $menu, array $attributes, ?MenuItem $item): void
    {
        if ($item instanceof MenuItem && $item->menu_id !== $menu->getKey()) {
            throw ValidationException::withMessages(['menu_id' => 'The item does not belong to this menu.']);
        }

        $label = $attributes['label'] ?? $item?->label;
        if ($label === null || $this->text($label) === '') {
            throw ValidationException::withMessages(['label' => 'A menu item label is required.']);
        }

        $type = $this->text($attributes['link_type'] ?? ($item instanceof MenuItem ? $item->link_type : 'custom'));
        if (! in_array($type, ['content', 'custom', 'system'], true)) {
            throw ValidationException::withMessages(['link_type' => 'The link type must be content, custom, or system.']);
        }

        $requirements = match ($type) {
            'content' => ['content_id' => 'A content identifier is required for content links.'],
            'system' => ['system_route' => 'A system route is required for system links.'],
            default => ['url' => 'A URL is required for custom links.'],
        };

        foreach ($requirements as $field => $message) {
            $value = array_key_exists($field, $attributes) ? $attributes[$field] : ($item?->getAttribute($field));
            if ($value === null || $this->text($value) === '') {
                throw ValidationException::withMessages([$field => $message]);
            }
        }

        if ($type === 'custom' && ! $this->isSafeUrl($attributes['url'] ?? $item?->url)) {
            throw ValidationException::withMessages(['url' => 'Custom links must use a relative path or HTTP(S) URL.']);
        }

        $parentId = $attributes['parent_id'] ?? $item?->parent_id;
        if ($parentId !== null) {
            $parent = MenuItem::query()->where('menu_id', $menu->getKey())->find($parentId);
            if (! $parent instanceof MenuItem || ($item instanceof MenuItem && $parent->is($item))) {
                throw ValidationException::withMessages(['parent_id' => 'The parent must be another item in this menu.']);
            }

            if ($item instanceof MenuItem && $this->isDescendant($parent, $item)) {
                throw ValidationException::withMessages(['parent_id' => 'An item cannot be nested beneath its own descendant.']);
            }
        }

        if (isset($attributes['visibility']) && ! is_array($attributes['visibility'])) {
            throw ValidationException::withMessages(['visibility' => 'Visibility rules must be an object.']);
        }
    }

    private function isDescendant(MenuItem $candidate, MenuItem $ancestor): bool
    {
        $seen = [];
        while (! isset($seen[$candidate->id])) {
            $seen[$candidate->id] = true;
            if ($candidate->parent_id === $ancestor->id) {
                return true;
            }
            $candidate = $candidate->parent()->first();
            if (! $candidate instanceof MenuItem) {
                break;
            }
        }

        return false;
    }

    private function text(mixed $value): string
    {
        return is_string($value) ? trim($value) : (is_scalar($value) ? trim((string) $value) : '');
    }

    private function isSafeUrl(mixed $value): bool
    {
        if (! is_string($value) || trim($value) === '' || preg_match('/[\x00-\x20\\\\]/', $value)) {
            return false;
        }
        if (str_starts_with($value, '/')) {
            return ! str_starts_with($value, '//');
        }
        $parsed = parse_url($value);

        return is_array($parsed) && in_array(strtolower((string) ($parsed['scheme'] ?? '')), ['http', 'https'], true) && isset($parsed['host']);
    }
}
