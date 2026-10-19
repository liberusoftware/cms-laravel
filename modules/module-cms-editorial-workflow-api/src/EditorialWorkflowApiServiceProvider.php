<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflowApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\EditorialWorkflowApi\Http\EditorialWorkflowController;

final class EditorialWorkflowApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('editorial-workflow-api', new ApiEndpoint('cms/editorial-workflows', EditorialWorkflowController::class, 'index', 'cms.editorial-workflows.index'));
        $registry->registerEndpoint('editorial-workflow-api', new ApiEndpoint('cms/editorial-workflows', EditorialWorkflowController::class, 'store', 'cms.editorial-workflows.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('editorial-workflow-api', new ApiEndpoint('cms/editorial-workflows/{publicId}', EditorialWorkflowController::class, 'show', 'cms.editorial-workflows.show'));
        $registry->registerEndpoint('editorial-workflow-api', new ApiEndpoint('cms/editorial-workflows/{publicId}/states', EditorialWorkflowController::class, 'state', 'cms.editorial-workflows.state', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('editorial-workflow-api', new ApiEndpoint('cms/editorial-workflows/{publicId}/transitions', EditorialWorkflowController::class, 'transition', 'cms.editorial-workflows.transition', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('editorial-workflow-api', new ApiEndpoint('cms/editorial-workflows/{publicId}/assignments', EditorialWorkflowController::class, 'assign', 'cms.editorial-workflows.assign', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('editorial-workflow-api', new ApiEndpoint('cms/editorial-workflows/{publicId}/reviews', EditorialWorkflowController::class, 'review', 'cms.editorial-workflows.review', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('editorial-workflow-api', new ApiEndpoint('cms/editorial-workflows/{publicId}/evidence', EditorialWorkflowController::class, 'evidence', 'cms.editorial-workflows.evidence', 'POST', ['abilities:content:write']));
    }
}
