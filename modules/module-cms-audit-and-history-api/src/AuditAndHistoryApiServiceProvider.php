<?php

declare(strict_types=1);

namespace Liberu\Cms\AuditAndHistoryApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AuditAndHistoryApi\Http\AuditAndHistoryController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class AuditAndHistoryApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }

        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('audit-and-history-api', new ApiEndpoint('cms/audit-and-history', AuditAndHistoryController::class, 'index', 'cms.audit-and-history.index', 'GET', ['abilities:audit:read']));
        $registry->registerEndpoint('audit-and-history-api', new ApiEndpoint('cms/audit-and-history/{audit}', AuditAndHistoryController::class, 'show', 'cms.audit-and-history.show', 'GET', ['abilities:audit:read']));
    }
}
