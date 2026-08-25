<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\TranslationManagementApi\Http\Controllers\TranslationManagementController;

final class TranslationManagementApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) return;
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/jobs', TranslationManagementController::class, 'index', 'cms.translation-management.jobs'));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/jobs/{publicId}', TranslationManagementController::class, 'show', 'cms.translation-management.job'));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/jobs', TranslationManagementController::class, 'create', 'cms.translation-management.jobs.create', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/jobs/{publicId}/source-changes', TranslationManagementController::class, 'sourceChange', 'cms.translation-management.source-changes.create', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/source-changes/{sourceChange}/translate', TranslationManagementController::class, 'translate', 'cms.translation-management.translate', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/source-changes/{sourceChange}/review', TranslationManagementController::class, 'review', 'cms.translation-management.review', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/jobs/{publicId}/reconcile', TranslationManagementController::class, 'reconcile', 'cms.translation-management.reconcile', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/jobs/{publicId}/queue', TranslationManagementController::class, 'queue', 'cms.translation-management.queue', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/source-changes/{sourceChange}/assign', TranslationManagementController::class, 'assign', 'cms.translation-management.assign', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/vendors', TranslationManagementController::class, 'vendor', 'cms.translation-management.vendor', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/memory', TranslationManagementController::class, 'memory', 'cms.translation-management.memory', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('translation-management-api', new ApiEndpoint('cms/translation-management/glossaries', TranslationManagementController::class, 'glossary', 'cms.translation-management.glossary', 'POST', ['abilities:content:write']));
    }
}
