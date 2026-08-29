<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectoryApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\IntegrationDirectoryApi\Http\IntegrationDirectoryController;

final class IntegrationDirectoryApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('integration-directory-api', new ApiEndpoint('cms/integrations', IntegrationDirectoryController::class, 'index', 'cms.integrations.index'));
            $r->registerEndpoint('integration-directory-api', new ApiEndpoint('cms/integrations', IntegrationDirectoryController::class, 'store', 'cms.integrations.store', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('integration-directory-api', new ApiEndpoint('cms/integrations/{integration}/health', IntegrationDirectoryController::class, 'health', 'cms.integrations.health', 'POST', ['abilities:content:write']));
        }
    }
}
