<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Hooks\Filters;

use Liberu\Cms\Contracts\Hooks\Filter;

/**
 * Admin-layer filter point: adapts a Filament resource's form schema before it
 * is built. A callback may append or replace entries in {@see $components} — the
 * canonical "add fields to another module's edit form" case — using the
 * read-only {@see $resource} key (e.g. "pages") to target a specific resource.
 *
 * Deliberately does not import Filament: the component type is threaded through
 * the {@see TComponent} generic so this contract stays framework-agnostic while
 * the emitting Filament resource keeps full static typing at its call site.
 *
 * @template TComponent of object
 *
 * @api This filter point is part of the public extension API.
 */
final class AdminFormSchemaFilter implements Filter
{
    /**
     * @param  list<TComponent>  $components  The form schema components; mutate to adapt.
     * @param  string  $resource  The resource key, e.g. "pages" (read-only context).
     */
    public function __construct(
        public array $components,
        public readonly string $resource,
    ) {}

    public function name(): string
    {
        return $this->resource.'.admin.form';
    }
}
