<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Team;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Liberu\Cms\Contracts\Tenancy\TenantModelResolverInterface;

/**
 * Host binding for the CMS tenancy contract: the tenant is a Team, active only
 * when multi-tenancy (Spatie teams) is enabled. Lets CMS module models scope to
 * the current team without importing this class.
 *
 * The current tenant is resolved from the Delivery API's request tenant context
 * first (set from the API token's Team), falling back to the Filament panel's
 * tenant — so the panel and the API share one source of truth.
 */
final class FilamentTenantResolver implements TenantModelResolverInterface
{
    public function tenantModel(): ?string
    {
        return config('permission.teams') ? Team::class : null;
    }

    public function currentTenantId(): int|string|null
    {
        $apiTenant = app(TenantContextInterface::class)->tenantId();

        if ($apiTenant !== null) {
            return $apiTenant;
        }

        $tenant = Filament::getTenant();

        return $tenant instanceof Model ? $tenant->getKey() : null;
    }
}
