<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Tenant;

use Liberu\Cms\Contracts\Tenancy\TenantModelResolverInterface;

/**
 * Default resolver for single-tenant installs: no tenant model, no current
 * tenant. The host binds its own resolver to enable multi-tenancy.
 */
final class NullTenantResolver implements TenantModelResolverInterface
{
    public function tenantModel(): ?string
    {
        return null;
    }

    public function currentTenantId(): int|string|null
    {
        return null;
    }
}
