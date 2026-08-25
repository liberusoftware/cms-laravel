<?php

declare(strict_types=1);

namespace Liberu\Cms\MultisiteApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\MultisiteApi\Http\MultisiteController;

final class MultisiteApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $read = ['abilities:multisite:view'];
        $write = ['abilities:multisite:update'];
        $registry->registerEndpoint('multisite-api', new ApiEndpoint('cms/multisite', MultisiteController::class, 'index', 'cms.multisite.list', 'GET', $read));
        $registry->registerEndpoint('multisite-api', new ApiEndpoint('cms/multisite', MultisiteController::class, 'store', 'cms.multisite.create', 'POST', ['abilities:multisite:create']));
        $registry->registerEndpoint('multisite-api', new ApiEndpoint('cms/multisite/{id}', MultisiteController::class, 'show', 'cms.multisite.get', 'GET', $read));
        $registry->registerEndpoint('multisite-api', new ApiEndpoint('cms/multisite/{id}', MultisiteController::class, 'update', 'cms.multisite.update', 'PATCH', $write));
        $registry->registerEndpoint('multisite-api', new ApiEndpoint('cms/multisite/{id}/admins', MultisiteController::class, 'admin', 'cms.multisite.admin', 'POST', $write));
        $registry->registerEndpoint('multisite-api', new ApiEndpoint('cms/multisite/{id}/quota', MultisiteController::class, 'quota', 'cms.multisite.quota', 'POST', $write));
        $registry->registerEndpoint('multisite-api', new ApiEndpoint('cms/multisite/{id}/references', MultisiteController::class, 'reference', 'cms.multisite.reference', 'POST', $write));
        $registry->registerEndpoint('multisite-api', new ApiEndpoint('cms/multisite/network-transition', MultisiteController::class, 'networkTransition', 'cms.multisite.network-transition', 'POST', ['abilities:multisite:manage']));
    }
}
