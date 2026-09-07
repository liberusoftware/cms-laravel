<?php

declare(strict_types=1);

namespace Liberu\Cms\CopilotApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\CopilotApi\Http\CopilotController;

final class CopilotApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('copilot-api', new ApiEndpoint('cms/cms-copilot/requests', CopilotController::class, 'submit', 'cms.copilot.requests.submit', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('copilot-api', new ApiEndpoint('cms/cms-copilot/requests/{request}/execute', CopilotController::class, 'execute', 'cms.copilot.requests.execute', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('copilot-api', new ApiEndpoint('cms/cms-copilot/requests/{request}/confirm', CopilotController::class, 'confirm', 'cms.copilot.requests.confirm', 'POST', ['abilities:content:write']));
    }
}
