<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionManagerApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\ExtensionManagerApi\Http\ExtensionManagerController;

final class ExtensionManagerApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('extension-manager-api', new ApiEndpoint('cms/extensions', ExtensionManagerController::class, 'index', 'cms.extensions.index'));
            $r->registerEndpoint('extension-manager-api', new ApiEndpoint('cms/extensions/{key}/enable', ExtensionManagerController::class, 'enable', 'cms.extensions.enable', 'POST', ['abilities:modules:manage']));
            $r->registerEndpoint('extension-manager-api', new ApiEndpoint('cms/extensions/{key}/disable', ExtensionManagerController::class, 'disable', 'cms.extensions.disable', 'POST', ['abilities:modules:manage']));
        }
    }
}
