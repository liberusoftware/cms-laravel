<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLockingApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentLockingApi\Http\ContentLockingController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentLockingApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('content-locking-api', new ApiEndpoint('cms/content-locking/locks', ContentLockingController::class, 'acquire', 'cms.content-locking.locks.acquire', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-locking-api', new ApiEndpoint('cms/content-locking/locks/{lock}/renew', ContentLockingController::class, 'renew', 'cms.content-locking.locks.renew', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-locking-api', new ApiEndpoint('cms/content-locking/locks/{lock}/compare', ContentLockingController::class, 'compare', 'cms.content-locking.locks.compare', 'POST'));
        $registry->registerEndpoint('content-locking-api', new ApiEndpoint('cms/content-locking/locks/{lock}', ContentLockingController::class, 'release', 'cms.content-locking.locks.release', 'DELETE', ['abilities:content:write']));
    }
}
