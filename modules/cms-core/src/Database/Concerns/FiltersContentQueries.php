<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Database\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Contracts\Hooks\Filters\ContentQueryFilter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;

/**
 * Routes a content repository's query through the {@see ContentQueryFilter} hook
 * point just before it executes, then re-asserts the tenant constraint.
 *
 * Re-applying the tenant `where` *after* the filter is what makes the hook safe:
 * a callback cannot widen the result set past the current tenant, even by
 * dropping the tenant global scope, because the explicit constraint added here
 * always wins. When no tenant context is set (panel, plain web, console) the
 * global scope is already a no-op and this adds nothing.
 */
trait FiltersContentQueries
{
    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    protected function filterContentQuery(string $key, Builder $query): Builder
    {
        app(HookBusInterface::class)->apply(new ContentQueryFilter($key, $query));

        $tenantId = app(TenantContextInterface::class)->tenantId();

        if ($tenantId !== null) {
            $query->where($query->getModel()->qualifyColumn('team_id'), $tenantId);
        }

        return $query;
    }
}
