<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrityApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentIntegrityApi\Http\ContentIntegrityController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentIntegrityApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('content-integrity-api', new ApiEndpoint('cms/content-integrity/findings', ContentIntegrityController::class, 'index', 'cms.content-integrity.findings.index'));
        $registry->registerEndpoint('content-integrity-api', new ApiEndpoint('cms/content-integrity/scans', ContentIntegrityController::class, 'scan', 'cms.content-integrity.scans.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-integrity-api', new ApiEndpoint('cms/content-integrity/scans/{scan}/findings', ContentIntegrityController::class, 'finding', 'cms.content-integrity.findings.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-integrity-api', new ApiEndpoint('cms/content-integrity/findings/{finding}/resolve', ContentIntegrityController::class, 'resolve', 'cms.content-integrity.findings.resolve', 'POST', ['abilities:content:write']));
    }
}
