<?php

declare(strict_types=1);

namespace Liberu\Cms\JoomlaMigrationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\JoomlaMigrationApi\Http\JoomlaMigrationController;

final class JoomlaMigrationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('joomla-migration-api', new ApiEndpoint('cms/joomla-migrations', JoomlaMigrationController::class, 'index', 'cms.joomla-migrations.index'));
            $r->registerEndpoint('joomla-migration-api', new ApiEndpoint('cms/joomla-migrations', JoomlaMigrationController::class, 'start', 'cms.joomla-migrations.start', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('joomla-migration-api', new ApiEndpoint('cms/joomla-migrations/{publicId}/records', JoomlaMigrationController::class, 'add', 'cms.joomla-migrations.records.create', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('joomla-migration-api', new ApiEndpoint('cms/joomla-migrations/{publicId}/records/{record}/process', JoomlaMigrationController::class, 'process', 'cms.joomla-migrations.records.process', 'POST', ['abilities:content:process']));
            $r->registerEndpoint('joomla-migration-api', new ApiEndpoint('cms/joomla-migrations/{publicId}/complete', JoomlaMigrationController::class, 'complete', 'cms.joomla-migrations.complete', 'POST', ['abilities:content:process']));
        }
    }
}
