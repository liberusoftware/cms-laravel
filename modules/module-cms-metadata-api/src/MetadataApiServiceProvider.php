<?php

declare(strict_types=1);

namespace Liberu\Cms\MetadataApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\MetadataApi\Http\MetadataController;

final class MetadataApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('metadata-api', new ApiEndpoint('cms/metadata/{subjectType}/{subjectId}', MetadataController::class, 'index', 'cms.metadata.index'));
            $registry->registerEndpoint('metadata-api', new ApiEndpoint('cms/metadata/{subjectType}/{subjectId}', MetadataController::class, 'set', 'cms.metadata.set', 'PUT', ['abilities:content:write']));
            $registry->registerEndpoint('metadata-api', new ApiEndpoint('cms/metadata/{subjectType}/{subjectId}/{key}', MetadataController::class, 'remove', 'cms.metadata.remove', 'DELETE', ['abilities:content:write']));
        }
    }
}
