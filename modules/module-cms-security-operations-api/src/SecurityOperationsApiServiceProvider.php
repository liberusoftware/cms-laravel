<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperationsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\SecurityOperationsApi\Http\SecurityOperationsController;

final class SecurityOperationsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('security-operations-api', new ApiEndpoint('cms/security-operations', SecurityOperationsController::class, 'index', 'cms.security-operations.index'));
        }
    }
}
