<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistantApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\MediaAssistantApi\Http\MediaAssistantController;

final class MediaAssistantApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('media-assistant-api', new ApiEndpoint('cms/media-assistant/suggestions', MediaAssistantController::class, 'index', 'cms.media-assistant.suggestions.index'));
        $registry->registerEndpoint('media-assistant-api', new ApiEndpoint('cms/media-assistant/suggestions', MediaAssistantController::class, 'store', 'cms.media-assistant.suggestions.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('media-assistant-api', new ApiEndpoint('cms/media-assistant/suggestions/{publicId}/review', MediaAssistantController::class, 'review', 'cms.media-assistant.suggestions.review', 'POST', ['abilities:content:write']));
    }
}
