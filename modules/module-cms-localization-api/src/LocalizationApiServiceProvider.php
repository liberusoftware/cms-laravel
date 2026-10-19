<?php

declare(strict_types=1);

namespace Liberu\Cms\LocalizationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\LocalizationApi\Http\LocalizationController;

final class LocalizationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('localization-api', new ApiEndpoint('cms/localization/locales', LocalizationController::class, 'locales', 'cms.localization.locales.index'));
        $registry->registerEndpoint('localization-api', new ApiEndpoint('cms/localization/variants', LocalizationController::class, 'variants', 'cms.localization.variants.index'));
        $registry->registerEndpoint('localization-api', new ApiEndpoint('cms/localization/locales', LocalizationController::class, 'storeLocale', 'cms.localization.locales.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('localization-api', new ApiEndpoint('cms/localization/variants', LocalizationController::class, 'variant', 'cms.localization.variants.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('localization-api', new ApiEndpoint('cms/localization/resolve', LocalizationController::class, 'resolve', 'cms.localization.resolve'));
        $registry->registerEndpoint('localization-api', new ApiEndpoint('cms/localization/completeness', LocalizationController::class, 'completeness', 'cms.localization.completeness'));
    }
}
