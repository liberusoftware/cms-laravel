<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Tenancy;

use Illuminate\Database\Eloquent\Model;

/**
 * Lets modules be tenant-scoped without importing the host's tenant model.
 *
 * The host application binds an implementation that names its own tenant model
 * (e.g. a Team). Modules resolve the class through this contract to build their
 * `team()` relationship, so no module ever references a host class.
 *
 * @internal Kernel wiring; not part of the public extension API and may change without notice.
 */
interface TenantModelResolverInterface
{
    /**
     * The Eloquent tenant model class, or null when multi-tenancy is disabled.
     *
     * @return class-string<Model>|null
     */
    public function tenantModel(): ?string;

    /**
     * The current tenant's key, or null when none is active.
     */
    public function currentTenantId(): int|string|null;
}
