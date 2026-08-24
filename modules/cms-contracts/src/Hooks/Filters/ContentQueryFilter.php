<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Hooks\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Contracts\Hooks\Filter;

/**
 * Data-layer filter point: adapts a content Eloquent query before it executes.
 * A callback may narrow, order, or eager-load through {@see $query} using the
 * read-only {@see $key} (a stable dot-notation query identity, e.g.
 * "pages.published") to target a specific query.
 *
 * Tenant safety: the emitting repository re-applies the tenant constraint as an
 * explicit `where` **after** this filter runs, so a callback that drops the
 * tenant global scope (e.g. `withoutGlobalScope(...)`) still cannot read another
 * tenant's rows. Do not rely on the filter to relax tenant isolation — it can't.
 *
 * @template TModel of Model
 *
 * @api This filter point is part of the public extension API.
 */
final readonly class ContentQueryFilter implements Filter
{
    /**
     * @param  string  $key  Stable query identity, e.g. "pages.published" (read-only).
     * @param  Builder<TModel>  $query  The query to adapt in place.
     */
    public function __construct(
        public string $key,
        public Builder $query,
    ) {}

    public function name(): string
    {
        return $this->key;
    }
}
