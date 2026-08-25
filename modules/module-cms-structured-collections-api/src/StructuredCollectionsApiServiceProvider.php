<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\StructuredCollectionsApi\Http\StructuredCollectionsController;

/** The legacy collections API provider remains the single route owner. */
final class StructuredCollectionsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) return;
        $r = $this->app->make(ApiResourceRegistryInterface::class);
        $r->registerEndpoint('structured-collections-api', new ApiEndpoint('cms/structured-collections', StructuredCollectionsController::class, 'index', 'cms.structured.collections.list'));
        $r->registerEndpoint('structured-collections-api', new ApiEndpoint('cms/structured-collections', StructuredCollectionsController::class, 'create', 'cms.structured.collections.create', 'POST', ['abilities:content:write']));
        $r->registerEndpoint('structured-collections-api', new ApiEndpoint('cms/structured-collections/{slug}', StructuredCollectionsController::class, 'show', 'cms.structured.collections.get'));
        $r->registerEndpoint('structured-collections-api', new ApiEndpoint('cms/structured-collections/{slug}', StructuredCollectionsController::class, 'update', 'cms.structured.collections.update', 'PATCH', ['abilities:content:write']));
        $r->registerEndpoint('structured-collections-api', new ApiEndpoint('cms/structured-collections/{slug}', StructuredCollectionsController::class, 'delete', 'cms.structured.collections.delete', 'DELETE', ['abilities:content:write']));
        $r->registerEndpoint('structured-collections-api', new ApiEndpoint('cms/structured-collections/{slug}/records', StructuredCollectionsController::class, 'records', 'cms.structured.collections.records'));
        $r->registerEndpoint('structured-collections-api', new ApiEndpoint('cms/structured-collections/{slug}/records', StructuredCollectionsController::class, 'createRecord', 'cms.structured.collections.records.create', 'POST', ['abilities:content:write']));
        $r->registerEndpoint('structured-collections-api', new ApiEndpoint('cms/structured-collections/{slug}/records/{record}', StructuredCollectionsController::class, 'record', 'cms.structured.collections.record'));
    }
}
