<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccessApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentAccessApi\Http\ContentAccessController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentAccessApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('content-access-api', new ApiEndpoint('cms/content-access/rules', ContentAccessController::class, 'store', 'cms.content-access.rules.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-access-api', new ApiEndpoint('cms/content-access/check', ContentAccessController::class, 'check', 'cms.content-access.check'));
        $registry->registerEndpoint('content-access-api', new ApiEndpoint('cms/content-access/private-links', ContentAccessController::class, 'privateLink', 'cms.content-access.private-links.store', 'POST', ['abilities:content:write']));
    }
}
