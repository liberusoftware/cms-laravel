<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagement;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\DocumentManagement\Services\DocumentManagementService;

final class DocumentManagementServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new DocumentManagementModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/document-management.php', 'document-management');
        $this->app->singleton(DocumentManagementService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('document-management', 'Document Management', AccessScope::Content, ['view', 'create', 'update', 'delete', 'download']));
        }
    }
}
