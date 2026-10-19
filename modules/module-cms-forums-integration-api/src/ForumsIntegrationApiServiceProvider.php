<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegrationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\ForumsIntegrationApi\Http\ForumsIntegrationController;

final class ForumsIntegrationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('forums-integration-api', new ApiEndpoint('cms/forums/references', ForumsIntegrationController::class, 'index', 'cms.forums.references'));
        $registry->registerEndpoint('forums-integration-api', new ApiEndpoint('cms/forums/references', ForumsIntegrationController::class, 'link', 'cms.forums.link', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('forums-integration-api', new ApiEndpoint('cms/forums/providers/{provider}/recent', ForumsIntegrationController::class, 'recent', 'cms.forums.recent'));
        $registry->registerEndpoint('forums-integration-api', new ApiEndpoint('cms/forums/references/{publicId}/moderation', ForumsIntegrationController::class, 'moderation', 'cms.forums.moderation'));
    }
}
