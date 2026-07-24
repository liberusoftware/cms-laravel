<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Tenant;

use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;

/**
 * In-memory, request-scoped tenant context. Bound as a singleton so the API
 * middleware, the tenancy resolver, and the tenant global scope all read the
 * same value for the lifetime of a request.
 */
final class RequestTenantContext implements TenantContextInterface
{
    private int|string|null $tenantId = null;

    public function tenantId(): int|string|null
    {
        return $this->tenantId;
    }

    public function setTenantId(int|string|null $tenantId): void
    {
        $this->tenantId = $tenantId;
    }
}
