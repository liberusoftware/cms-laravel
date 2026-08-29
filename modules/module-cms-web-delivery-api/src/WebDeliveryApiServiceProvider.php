<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDeliveryApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\WebDeliveryApi\Http\Controllers\WebDeliveryController;

final class WebDeliveryApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('web-delivery-api', new ApiEndpoint('cms/web-delivery/routes', WebDeliveryController::class, 'index', 'cms.web-delivery.routes'));
        $registry->registerEndpoint('web-delivery-api', new ApiEndpoint('cms/web-delivery/routes/{path}', WebDeliveryController::class, 'show', 'cms.web-delivery.route'));
        $registry->registerEndpoint('web-delivery-api', new ApiEndpoint('cms/web-delivery/routes', WebDeliveryController::class, 'create', 'cms.web-delivery.routes.create', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('web-delivery-api', new ApiEndpoint('cms/web-delivery/routes/{route}', WebDeliveryController::class, 'update', 'cms.web-delivery.route.update', 'PATCH', ['abilities:content:write']));
        $registry->registerEndpoint('web-delivery-api', new ApiEndpoint('cms/web-delivery/routes/{route}', WebDeliveryController::class, 'destroy', 'cms.web-delivery.route.delete', 'DELETE', ['abilities:content:write']));
        $registry->registerEndpoint('web-delivery-api', new ApiEndpoint('cms/web-delivery/resolve', WebDeliveryController::class, 'resolve', 'cms.web-delivery.resolve'));
        $registry->registerEndpoint('web-delivery-api', new ApiEndpoint('cms/web-delivery/routes/{route}/preview-token', WebDeliveryController::class, 'previewToken', 'cms.web-delivery.preview', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('web-delivery-api', new ApiEndpoint('cms/web-delivery/invalidate', WebDeliveryController::class, 'invalidate', 'cms.web-delivery.invalidate', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('web-delivery-api', new ApiEndpoint('cms/web-delivery/routes/{route}/maintenance', WebDeliveryController::class, 'maintenance', 'cms.web-delivery.maintenance', 'POST', ['abilities:content:write']));
    }
}
