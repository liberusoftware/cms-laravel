<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistantApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\ExperienceAssistantApi\Http\ExperienceAssistantController;

final class ExperienceAssistantApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('experience-assistant-api', new ApiEndpoint('cms/experience-assistant/suggestions', ExperienceAssistantController::class, 'index', 'cms.experience-assistant.suggestions.index'));
        $registry->registerEndpoint('experience-assistant-api', new ApiEndpoint('cms/experience-assistant/suggestions', ExperienceAssistantController::class, 'store', 'cms.experience-assistant.suggestions.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('experience-assistant-api', new ApiEndpoint('cms/experience-assistant/check', ExperienceAssistantController::class, 'check', 'cms.experience-assistant.check', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('experience-assistant-api', new ApiEndpoint('cms/experience-assistant/suggestions/{publicId}/approve', ExperienceAssistantController::class, 'approve', 'cms.experience-assistant.suggestions.approve', 'POST', ['abilities:content:write']));
    }
}
