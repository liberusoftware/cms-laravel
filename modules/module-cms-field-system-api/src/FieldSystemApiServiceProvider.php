<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentTypes\Queries\FieldSchemaQuery;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\FieldSystemApi\Http\Controllers\FieldSchemaController;

final class FieldSystemApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FieldSchemaQuery::class);

        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint(
                'field-system',
                new ApiEndpoint('field-schemas/{type}', FieldSchemaController::class, 'show', 'field-schemas.show'),
            );
        }
    }
}
