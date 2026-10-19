<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagementApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\DigitalAssetManagementApi\Http\DigitalAssetController;

final class DigitalAssetManagementApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('digital-asset-management-api', new ApiEndpoint('cms/digital-asset-management/assets', DigitalAssetController::class, 'index', 'cms.digital-assets.index'));
        $registry->registerEndpoint('digital-asset-management-api', new ApiEndpoint('cms/digital-asset-management/assets', DigitalAssetController::class, 'store', 'cms.digital-assets.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('digital-asset-management-api', new ApiEndpoint('cms/digital-asset-management/assets/{asset}/approve', DigitalAssetController::class, 'approve', 'cms.digital-assets.approve', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('digital-asset-management-api', new ApiEndpoint('cms/digital-asset-management/assets/{asset}/renditions', DigitalAssetController::class, 'rendition', 'cms.digital-assets.renditions.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('digital-asset-management-api', new ApiEndpoint('cms/digital-asset-management/assets/{asset}/distribution', DigitalAssetController::class, 'distribute', 'cms.digital-assets.distribute', 'POST', ['abilities:content:write']));
    }
}
