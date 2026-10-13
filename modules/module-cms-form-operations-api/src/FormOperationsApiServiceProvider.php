<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperationsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\FormOperationsApi\Http\FormOperationsController;

final class FormOperationsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('form-operations-api', new ApiEndpoint('cms/form-operations/submissions', FormOperationsController::class, 'index', 'cms.form-operations.submissions'));
        $registry->registerEndpoint('form-operations-api', new ApiEndpoint('cms/form-operations/submissions', FormOperationsController::class, 'submit', 'cms.form-operations.submit', 'POST'));
        $registry->registerEndpoint('form-operations-api', new ApiEndpoint('cms/form-operations/submissions/{publicId}/export', FormOperationsController::class, 'export', 'cms.form-operations.export', 'POST', ['abilities:content:write']));
    }
}
