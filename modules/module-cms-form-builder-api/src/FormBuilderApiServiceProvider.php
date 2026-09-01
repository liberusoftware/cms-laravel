<?php

declare(strict_types=1);

namespace Liberu\Cms\FormBuilderApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\FormBuilderApi\Http\FormBuilderController;

final class FormBuilderApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('form-builder-api', new ApiEndpoint('cms/forms/validate', FormBuilderController::class, 'validateForm', 'cms.forms.validate', 'POST'));
        $registry->registerEndpoint('form-builder-api', new ApiEndpoint('cms/forms/visible-fields', FormBuilderController::class, 'visibleFields', 'cms.forms.visible-fields', 'POST'));
        $registry->registerEndpoint('form-builder-api', new ApiEndpoint('cms/forms/calculate', FormBuilderController::class, 'calculate', 'cms.forms.calculate', 'POST'));
        $registry->registerEndpoint('form-builder-api', new ApiEndpoint('cms/forms/confirmation', FormBuilderController::class, 'confirmation', 'cms.forms.confirmation', 'POST'));
        $registry->registerEndpoint('form-builder-api', new ApiEndpoint('cms/forms/{publicId}/embed', FormBuilderController::class, 'embed', 'cms.forms.embed'));
    }
}
