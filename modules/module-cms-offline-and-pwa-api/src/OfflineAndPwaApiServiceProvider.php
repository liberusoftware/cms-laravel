<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwaApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\OfflineAndPwaApi\Http\OfflineAndPwaController;

final class OfflineAndPwaApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $write = ['abilities:content:write'];
            $registry->registerEndpoint('offline-and-pwa-api', new ApiEndpoint('cms/offline-and-pwa', OfflineAndPwaController::class, 'index', 'cms.offline-and-pwa.list'));
            $registry->registerEndpoint('offline-and-pwa-api', new ApiEndpoint('cms/offline-and-pwa', OfflineAndPwaController::class, 'store', 'cms.offline-and-pwa.create', 'POST', $write));
            $registry->registerEndpoint('offline-and-pwa-api', new ApiEndpoint('cms/offline-and-pwa/{id}', OfflineAndPwaController::class, 'showById', 'cms.offline-and-pwa.get'));
            $registry->registerEndpoint('offline-and-pwa-api', new ApiEndpoint('cms/offline-and-pwa/{id}', OfflineAndPwaController::class, 'update', 'cms.offline-and-pwa.update', 'PATCH', $write));
            $registry->registerEndpoint('offline-and-pwa-api', new ApiEndpoint('cms/offline-and-pwa/{id}', OfflineAndPwaController::class, 'destroy', 'cms.offline-and-pwa.delete', 'DELETE', $write));
            $registry->registerEndpoint('offline-and-pwa-api', new ApiEndpoint('cms/offline-and-pwa/{id}/cache-policy', OfflineAndPwaController::class, 'cachePolicy', 'cms.offline-and-pwa.cache-policy', 'POST', $write));
            $registry->registerEndpoint('offline-and-pwa-api', new ApiEndpoint('cms/offline-and-pwa/{id}/publish-update', OfflineAndPwaController::class, 'publishUpdate', 'cms.offline-and-pwa.publish-update', 'POST', $write));
        }
    }
}
