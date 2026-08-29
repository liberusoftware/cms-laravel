<?php

declare(strict_types=1);

namespace Liberu\Cms\LayoutBuilderApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\LayoutBuilderApi\Http\LayoutBuilderController;

final class LayoutBuilderApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('layout-builder-api', new ApiEndpoint('cms/layouts/{targetType}/{targetId}', LayoutBuilderController::class, 'show', 'cms.layouts.show'));
            $r->registerEndpoint('layout-builder-api', new ApiEndpoint('cms/layouts', LayoutBuilderController::class, 'store', 'cms.layouts.store', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('layout-builder-api', new ApiEndpoint('cms/layouts/{layout}/publish', LayoutBuilderController::class, 'publish', 'cms.layouts.publish', 'POST', ['abilities:content:write']));
        }
    }
}
