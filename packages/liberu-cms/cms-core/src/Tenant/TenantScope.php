<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Tenant;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;

/**
 * Constrains a tenant-scoped model's queries to the tenant named in the request
 * tenant context. The context is only set where there is no Filament panel to do
 * the scoping — the Delivery API sets it from the token's Team — so API reads see
 * only that tenant's content. Inside the Filament panel the context is empty and
 * this scope is a no-op, leaving the panel's own tenancy scope as the single
 * mechanism there; likewise plain web and console reads are untouched.
 *
 * @implements Scope<Model>
 */
final class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = app(TenantContextInterface::class)->tenantId();

        if ($tenantId === null) {
            return;
        }

        $builder->where($model->qualifyColumn('team_id'), $tenantId);
    }
}
