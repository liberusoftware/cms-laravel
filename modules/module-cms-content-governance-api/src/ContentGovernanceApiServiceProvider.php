<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernanceApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentGovernanceApi\Http\ContentGovernanceController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentGovernanceApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('content-governance-api', new ApiEndpoint('cms/content-governance/records', ContentGovernanceController::class, 'index', 'cms.content-governance.records.index'));
        $registry->registerEndpoint('content-governance-api', new ApiEndpoint('cms/content-governance/records', ContentGovernanceController::class, 'store', 'cms.content-governance.records.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-governance-api', new ApiEndpoint('cms/content-governance/records/{record}/legal-hold', ContentGovernanceController::class, 'hold', 'cms.content-governance.records.hold', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-governance-api', new ApiEndpoint('cms/content-governance/records/{record}/evidence', ContentGovernanceController::class, 'evidence', 'cms.content-governance.records.evidence', 'POST', ['abilities:content:write']));
    }
}
