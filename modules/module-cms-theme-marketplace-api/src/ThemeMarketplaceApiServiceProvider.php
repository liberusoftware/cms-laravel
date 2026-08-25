<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\ThemeMarketplaceApi\Http\MarketplaceController;

final class ThemeMarketplaceApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('theme-marketplace-api', new ApiEndpoint('cms/theme-marketplace', MarketplaceController::class, 'index', 'cms.theme-marketplace.index'));
            $r->registerEndpoint('theme-marketplace-api', new ApiEndpoint('cms/theme-marketplace/{key}', MarketplaceController::class, 'show', 'cms.theme-marketplace.show'));
            $r->registerEndpoint('theme-marketplace-api', new ApiEndpoint('cms/theme-marketplace', MarketplaceController::class, 'publish', 'cms.theme-marketplace.publish', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('theme-marketplace-api', new ApiEndpoint('cms/theme-marketplace/{key}/install', MarketplaceController::class, 'install', 'cms.theme-marketplace.install', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('theme-marketplace-api', new ApiEndpoint('cms/theme-marketplace/{key}/rate', MarketplaceController::class, 'rate', 'cms.theme-marketplace.rate', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('theme-marketplace-api', new ApiEndpoint('cms/theme-marketplace/{key}/security-review', MarketplaceController::class, 'security', 'cms.theme-marketplace.security', 'POST', ['abilities:content:write']));
        }
    }
}
