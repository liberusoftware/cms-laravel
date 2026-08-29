<?php

declare(strict_types=1);

namespace Liberu\Cms\DrupalMigrationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\DrupalMigrationApi\Http\DrupalMigrationController;

final class DrupalMigrationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('drupal-migration-api', new ApiEndpoint('cms/drupal-migrations', DrupalMigrationController::class, 'index', 'cms.drupal-migrations.index'));
            $r->registerEndpoint('drupal-migration-api', new ApiEndpoint('cms/drupal-migrations', DrupalMigrationController::class, 'start', 'cms.drupal-migrations.start', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('drupal-migration-api', new ApiEndpoint('cms/drupal-migrations/{publicId}/records', DrupalMigrationController::class, 'add', 'cms.drupal-migrations.records.create', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('drupal-migration-api', new ApiEndpoint('cms/drupal-migrations/{publicId}/records/{record}/process', DrupalMigrationController::class, 'process', 'cms.drupal-migrations.records.process', 'POST', ['abilities:content:process']));
            $r->registerEndpoint('drupal-migration-api', new ApiEndpoint('cms/drupal-migrations/{publicId}/complete', DrupalMigrationController::class, 'complete', 'cms.drupal-migrations.complete', 'POST', ['abilities:content:process']));
        }
    }
}
