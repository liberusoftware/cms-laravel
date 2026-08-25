<?php

declare(strict_types=1);

namespace Liberu\Cms\RevisionsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\RevisionsApi\Http\RevisionController;

final class RevisionsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('revisions-api', new ApiEndpoint('cms/revisions/{type}/{id}', RevisionController::class, 'index', 'cms.revisions.index'));
            $registry->registerEndpoint('revisions-api', new ApiEndpoint('cms/revisions/{type}/{id}', RevisionController::class, 'create', 'cms.revisions.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('revisions-api', new ApiEndpoint('cms/revisions/{type}/{id}/autosave', RevisionController::class, 'autosave', 'cms.revisions.autosave', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('revisions-api', new ApiEndpoint('cms/revisions/{type}/{id}/prune', RevisionController::class, 'prune', 'cms.revisions.prune', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('revisions-restore-api', new ApiEndpoint('cms/revisions/{revision}/restore', RevisionController::class, 'restore', 'cms.revisions.restore', 'POST'));
            $registry->registerEndpoint('revisions-api', new ApiEndpoint('cms/revisions/{from}/compare/{to}', RevisionController::class, 'compare', 'cms.revisions.compare'));
            $registry->registerEndpoint('revisions-api', new ApiEndpoint('cms/revisions/{revision}/branch', RevisionController::class, 'branch', 'cms.revisions.branch', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('revisions-api', new ApiEndpoint('cms/revisions/{revision}/publish', RevisionController::class, 'publish', 'cms.revisions.publish', 'POST', ['abilities:content:publish']));
        }
    }
}
