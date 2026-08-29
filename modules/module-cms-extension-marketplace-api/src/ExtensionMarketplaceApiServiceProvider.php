<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplaceApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\ExtensionMarketplaceApi\Http\ExtensionMarketplaceController;

final class ExtensionMarketplaceApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('extension-marketplace-api', new ApiEndpoint('cms/extension-marketplace', ExtensionMarketplaceController::class, 'index', 'cms.extension-marketplace.index'));
        $registry->registerEndpoint('extension-marketplace-api', new ApiEndpoint('cms/extension-marketplace/{key}', ExtensionMarketplaceController::class, 'show', 'cms.extension-marketplace.show'));
        $registry->registerEndpoint('extension-marketplace-api', new ApiEndpoint('cms/extension-marketplace', ExtensionMarketplaceController::class, 'store', 'cms.extension-marketplace.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('extension-marketplace-api', new ApiEndpoint('cms/extension-marketplace/{key}/security', ExtensionMarketplaceController::class, 'security', 'cms.extension-marketplace.security', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('extension-marketplace-api', new ApiEndpoint('cms/extension-marketplace/{key}/publish', ExtensionMarketplaceController::class, 'publish', 'cms.extension-marketplace.publish', 'POST', ['abilities:content:write']));
    }
}
