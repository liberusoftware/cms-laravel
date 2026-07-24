<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Tenancy;

/**
 * The request-scoped holder that names the current tenant during a Delivery API
 * request. The API auth middleware sets it from the authenticated Team; the host
 * tenancy resolver reads it before falling back to the Filament panel's tenant,
 * so the panel and the API share one source of truth for tenancy.
 */
interface TenantContextInterface
{
    /**
     * The current tenant's key, or null when none is set for this request.
     */
    public function tenantId(): int|string|null;

    public function setTenantId(int|string|null $tenantId): void;
}
