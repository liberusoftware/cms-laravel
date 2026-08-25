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
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('offline-and-pwa-api', new ApiEndpoint('cms/offline-and-pwa', OfflineAndPwaController::class, 'show', 'cms.offline-and-pwa.show'));
        }
    }
}
