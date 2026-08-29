<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperimentationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\ExperimentationApi\Http\ExperimentationController;

final class ExperimentationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('experimentation-api', new ApiEndpoint('cms/experimentation', ExperimentationController::class, 'index', 'cms.experimentation.index'));
        $registry->registerEndpoint('experimentation-api', new ApiEndpoint('cms/experimentation/{key}', ExperimentationController::class, 'show', 'cms.experimentation.show'));
        $registry->registerEndpoint('experimentation-api', new ApiEndpoint('cms/experimentation', ExperimentationController::class, 'store', 'cms.experimentation.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('experimentation-api', new ApiEndpoint('cms/experimentation/{key}/start', ExperimentationController::class, 'start', 'cms.experimentation.start', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('experimentation-api', new ApiEndpoint('cms/experimentation/{key}/allocate', ExperimentationController::class, 'allocate', 'cms.experimentation.allocate', 'POST'));
        $registry->registerEndpoint('experimentation-api', new ApiEndpoint('cms/experimentation/{key}/promote', ExperimentationController::class, 'promote', 'cms.experimentation.promote', 'POST', ['abilities:content:write']));
    }
}
