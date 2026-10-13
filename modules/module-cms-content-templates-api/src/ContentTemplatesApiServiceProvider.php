<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplatesApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentTemplatesApi\Http\ContentTemplatesController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentTemplatesApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('content-templates-api', new ApiEndpoint('cms/content-templates/templates', ContentTemplatesController::class, 'index', 'cms.content-templates.index'));
        $registry->registerEndpoint('content-templates-api', new ApiEndpoint('cms/content-templates/templates', ContentTemplatesController::class, 'store', 'cms.content-templates.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-templates-api', new ApiEndpoint('cms/content-templates/templates/{template}/publish', ContentTemplatesController::class, 'publish', 'cms.content-templates.publish', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-templates-api', new ApiEndpoint('cms/content-templates/templates/{template}/lock', ContentTemplatesController::class, 'lock', 'cms.content-templates.lock', 'POST', ['abilities:content:write']));
    }
}
