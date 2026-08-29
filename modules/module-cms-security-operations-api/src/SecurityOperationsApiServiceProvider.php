<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperationsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\SecurityOperationsApi\Http\SecurityOperationsController;

final class SecurityOperationsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('security-operations-api', new ApiEndpoint('cms/security-operations', SecurityOperationsController::class, 'index', 'cms.security-operations.index'));
            $r->registerEndpoint('security-operations-api', new ApiEndpoint('cms/security-operations/inventory', SecurityOperationsController::class, 'inventory', 'cms.security-operations.inventory', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('security-operations-api', new ApiEndpoint('cms/security-operations/provenance', SecurityOperationsController::class, 'provenance', 'cms.security-operations.provenance', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('security-operations-api', new ApiEndpoint('cms/security-operations/integrity', SecurityOperationsController::class, 'integrity', 'cms.security-operations.integrity', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('security-operations-api', new ApiEndpoint('cms/security-operations/advisories', SecurityOperationsController::class, 'advisory', 'cms.security-operations.advisory', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('security-operations-api', new ApiEndpoint('cms/security-operations/scans', SecurityOperationsController::class, 'scan', 'cms.security-operations.scan', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('security-operations-api', new ApiEndpoint('cms/security-operations/incidents', SecurityOperationsController::class, 'incident', 'cms.security-operations.incident', 'POST', ['abilities:content:write']));
        }
    }
}
