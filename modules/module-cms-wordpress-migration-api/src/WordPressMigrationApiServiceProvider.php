<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigrationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\WordPressMigrationApi\Http\WordPressMigrationController;

final class WordPressMigrationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        } $r = $this->app->make(ApiResourceRegistryInterface::class);
        $r->registerEndpoint('wordpress-migration-api', new ApiEndpoint('cms/wordpress-migration', WordPressMigrationController::class, 'index', 'cms.wordpress-migration.index'));
        $r->registerEndpoint('wordpress-migration-api', new ApiEndpoint('cms/wordpress-migration', WordPressMigrationController::class, 'create', 'cms.wordpress-migration.create', 'POST', ['abilities:content:write']));
        $r->registerEndpoint('wordpress-migration-api', new ApiEndpoint('cms/wordpress-migration/{publicId}', WordPressMigrationController::class, 'show', 'cms.wordpress-migration.show'));
        $r->registerEndpoint('wordpress-migration-api', new ApiEndpoint('cms/wordpress-migration/{publicId}/records', WordPressMigrationController::class, 'records', 'cms.wordpress-migration.records'));
        $r->registerEndpoint('wordpress-migration-api', new ApiEndpoint('cms/wordpress-migration/{publicId}/records', WordPressMigrationController::class, 'addRecord', 'cms.wordpress-migration.records.create', 'POST', ['abilities:content:write']));
        $r->registerEndpoint('wordpress-migration-api', new ApiEndpoint('cms/wordpress-migration/{publicId}/records/{record}/process', WordPressMigrationController::class, 'process', 'cms.wordpress-migration.record.process', 'POST', ['abilities:content:process']));
        $r->registerEndpoint('wordpress-migration-api', new ApiEndpoint('cms/wordpress-migration/{publicId}/complete', WordPressMigrationController::class, 'complete', 'cms.wordpress-migration.complete', 'POST', ['abilities:content:process']));
        $r->registerEndpoint('wordpress-migration-api', new ApiEndpoint('cms/wordpress-migration/{publicId}/fail', WordPressMigrationController::class, 'fail', 'cms.wordpress-migration.fail', 'POST', ['abilities:content:process']));
    }
}
