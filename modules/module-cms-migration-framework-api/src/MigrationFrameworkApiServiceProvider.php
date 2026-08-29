<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFrameworkApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\MigrationFrameworkApi\Http\MigrationFrameworkController;

final class MigrationFrameworkApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('migration-framework-api', new ApiEndpoint('cms/migrations', MigrationFrameworkController::class, 'index', 'cms.migrations.index'));
            $r->registerEndpoint('migration-framework-api', new ApiEndpoint('cms/migrations', MigrationFrameworkController::class, 'start', 'cms.migrations.start', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('migration-framework-api', new ApiEndpoint('cms/migrations/{publicId}/records', MigrationFrameworkController::class, 'add', 'cms.migrations.records.create', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('migration-framework-api', new ApiEndpoint('cms/migrations/{publicId}/records/{record}/process', MigrationFrameworkController::class, 'process', 'cms.migrations.records.process', 'POST', ['abilities:content:process']));
            $r->registerEndpoint('migration-framework-api', new ApiEndpoint('cms/migrations/{publicId}/complete', MigrationFrameworkController::class, 'complete', 'cms.migrations.complete', 'POST', ['abilities:content:process']));
        }
    }
}
