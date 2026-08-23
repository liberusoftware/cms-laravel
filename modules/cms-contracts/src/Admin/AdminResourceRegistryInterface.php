<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Admin;

/**
 * The catalogue of admin (Filament) surfaces modules contribute to the panel.
 *
 * A module announces the resource and/or page classes it owns during the
 * register phase, tagged with its module key. The Admin module reads the
 * catalogue when it builds the panel and registers them — so the admin surface
 * tracks the installed modules without the Admin module ever importing one.
 *
 * Resources are model-backed CRUD surfaces; pages are standalone Filament pages
 * (used by modules whose state is a registry rather than an Eloquent model, such
 * as themes and widgets).
 *
 * @api This interface is part of the public extension API.
 */
interface AdminResourceRegistryInterface
{
    /**
     * Announce a Filament resource owned by a module.
     *
     * @param  string  $moduleKey  The owning module's key, e.g. "menus".
     * @param  string  $resourceClass  A Filament resource class name.
     */
    public function registerResource(string $moduleKey, string $resourceClass): void;

    /**
     * Every registered resource, grouped by the owning module key.
     *
     * @return array<string, array<int, string>>
     */
    public function resources(): array;

    /**
     * Announce a Filament page owned by a module.
     *
     * @param  string  $moduleKey  The owning module's key, e.g. "themes".
     * @param  string  $pageClass  A Filament page class name.
     */
    public function registerPage(string $moduleKey, string $pageClass): void;

    /**
     * Every registered page, grouped by the owning module key.
     *
     * @return array<string, array<int, string>>
     */
    public function pages(): array;
}
