<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagementApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ConfigurationManagementApi\Http\ConfigurationController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ConfigurationManagementApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $r = $this->app->make(ApiResourceRegistryInterface::class);
        $p = 'cms.configuration-management.';
        $r->registerEndpoint('configuration-management-api', new ApiEndpoint('cms/configuration-management', ConfigurationController::class, 'index', $p.'list'));
        $r->registerEndpoint('configuration-management-api', new ApiEndpoint('cms/configuration-management', ConfigurationController::class, 'store', $p.'create', 'POST', ['abilities:configuration-management:export']));
        $r->registerEndpoint('configuration-management-api', new ApiEndpoint('cms/configuration-management/{release}', ConfigurationController::class, 'show', $p.'get'));
        $r->registerEndpoint('configuration-management-api', new ApiEndpoint('cms/configuration-management/{release}/compare/{other}', ConfigurationController::class, 'compare', $p.'compare'));
        $r->registerEndpoint('configuration-management-api', new ApiEndpoint('cms/configuration-management/{release}/promote', ConfigurationController::class, 'promote', $p.'promote', 'POST', ['abilities:configuration-management:promote']));
        $r->registerEndpoint('configuration-management-api', new ApiEndpoint('cms/configuration-management/{release}/rollback', ConfigurationController::class, 'rollback', $p.'rollback', 'POST', ['abilities:configuration-management:rollback']));
    }
}
