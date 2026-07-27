<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Hooks\Filters;

use Liberu\Cms\Contracts\Hooks\Filter;

/**
 * Delivery-layer filter point: reshapes a Delivery API resource's serialized
 * payload before it is returned. A callback may add, remove, or rewrite entries
 * in {@see $data}, discriminating on {@see $model} (the resource's underlying
 * model — typically an Eloquent model) so it targets only the resources it means
 * to. Fires for every content resource that opts into the shared
 * `FiltersApiResource` seam (Pages, Posts, Menus, Content-Entries); a resource
 * wrapping a non-model shape (e.g. search hits) is not covered.
 *
 * @api This filter point is part of the public extension API.
 */
final class ApiResourceFilter implements Filter
{
    /**
     * @param  array<string, mixed>  $data  The serialized payload; mutate to reshape.
     * @param  mixed  $model  The resource's underlying model/data (read-only context).
     */
    public function __construct(
        public array $data,
        public readonly mixed $model,
    ) {}

    public function name(): string
    {
        return 'api.resource';
    }
}
