<?php

declare(strict_types=1);

namespace Liberu\Cms\Revisions;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Revisions\Services\RevisionService;

final class RevisionsServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new RevisionsModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(RevisionService::class);
    }

    protected function bootModule(): void
    {
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('revisions', 'Revisions', AccessScope::Content, ['view', 'create', 'restore', 'publish', 'delete']));
        }
    }
}
