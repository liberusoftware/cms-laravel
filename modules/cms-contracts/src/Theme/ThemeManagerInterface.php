<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Theme;

/**
 * Registers themes, tracks the active one, and resolves view overrides along
 * the theme inheritance chain (child theme wins over its parent).
 *
 * @api This interface is part of the public extension API.
 */
interface ThemeManagerInterface
{
    public function register(ThemeInterface $theme): void;

    /**
     * @return array<string, ThemeInterface>
     */
    public function all(): array;

    public function get(string $key): ?ThemeInterface;

    /**
     * The currently active theme, or null when none is set/registered.
     */
    public function active(): ?ThemeInterface;

    public function setActive(string $key): void;

    /**
     * The theme and its ancestors, nearest first, for override resolution.
     * Defaults to the active theme when no key is given.
     *
     * @return array<int, ThemeInterface>
     */
    public function inheritanceChain(?string $key = null): array;

    /**
     * Resolve a dot-notation view name to a file path, preferring the active
     * theme and falling back through its ancestors. Null when unresolved.
     */
    public function resolveView(string $view): ?string;
}
