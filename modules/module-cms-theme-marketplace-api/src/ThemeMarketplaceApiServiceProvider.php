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
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('theme-marketplace-api', new ApiEndpoint('cms/theme-marketplace', MarketplaceController::class, 'index', 'cms.theme-marketplace.index'));
        }
    }
}
