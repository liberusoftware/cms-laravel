<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistantApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AccessibilityAssistantApi\Http\AccessibilityAssistantController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class AccessibilityAssistantApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('accessibility-assistant-api', new ApiEndpoint('cms/accessibility-assistant/analyze', AccessibilityAssistantController::class, 'analyze', 'cms.accessibility-assistant.analyze', 'POST', ['abilities:content:read']));
    }
}
