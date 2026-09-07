<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagementApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\DocumentManagementApi\Http\DocumentsController;

final class DocumentManagementApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('document-management-api', new ApiEndpoint('cms/documents', DocumentsController::class, 'index', 'cms.documents.index'));
        $registry->registerEndpoint('document-management-api', new ApiEndpoint('cms/documents', DocumentsController::class, 'store', 'cms.documents.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('document-management-api', new ApiEndpoint('cms/documents/{document}/status', DocumentsController::class, 'status', 'cms.documents.status', 'POST', ['abilities:content:write']));
    }
}
