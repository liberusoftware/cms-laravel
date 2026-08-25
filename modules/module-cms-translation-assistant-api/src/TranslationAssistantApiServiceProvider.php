<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistantApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\TranslationAssistantApi\Http\TranslationAssistantController;

final class TranslationAssistantApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant', TranslationAssistantController::class, 'index', 'cms.translation-assistant.index'));
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant', TranslationAssistantController::class, 'create', 'cms.translation-assistant.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant/{draft}', TranslationAssistantController::class, 'show', 'cms.translation-assistant.show'));
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant/{draft}', TranslationAssistantController::class, 'update', 'cms.translation-assistant.update', 'PATCH', ['abilities:content:write']));
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant/{draft}', TranslationAssistantController::class, 'delete', 'cms.translation-assistant.delete', 'DELETE', ['abilities:content:write']));
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant/{draft}/review', TranslationAssistantController::class, 'review', 'cms.translation-assistant.review', 'POST', ['abilities:content:review']));
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant/{draft}/check', TranslationAssistantController::class, 'check', 'cms.translation-assistant.check', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant/glossary', TranslationAssistantController::class, 'glossary', 'cms.translation-assistant.glossary'));
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant/glossary', TranslationAssistantController::class, 'createGlossary', 'cms.translation-assistant.glossary.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant/style-rules', TranslationAssistantController::class, 'styleRules', 'cms.translation-assistant.style-rules'));
            $registry->registerEndpoint('translation-assistant-api', new ApiEndpoint('cms/translation-assistant/style-rules', TranslationAssistantController::class, 'createStyleRule', 'cms.translation-assistant.style-rules.create', 'POST', ['abilities:content:write']));
        }
    }
}
