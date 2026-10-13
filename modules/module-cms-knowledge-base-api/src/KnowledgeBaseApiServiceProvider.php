<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBaseApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\KnowledgeBaseApi\Http\KnowledgeBaseController;

final class KnowledgeBaseApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }

        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('knowledge-base-api', new ApiEndpoint('cms/knowledge-base', KnowledgeBaseController::class, 'index', 'cms.knowledge-base.index'));
        $registry->registerEndpoint('knowledge-base-api', new ApiEndpoint('cms/knowledge-base/{key}', KnowledgeBaseController::class, 'show', 'cms.knowledge-base.show'));
        $registry->registerEndpoint('knowledge-base-api', new ApiEndpoint('cms/knowledge-base', KnowledgeBaseController::class, 'store', 'cms.knowledge-base.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('knowledge-base-api', new ApiEndpoint('cms/knowledge-base/{key}/publish', KnowledgeBaseController::class, 'publish', 'cms.knowledge-base.publish', 'POST', ['abilities:content:write']));
    }
}
