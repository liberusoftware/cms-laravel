<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernance;

use Liberu\Cms\ContentGovernance\Services\ContentGovernanceService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ContentGovernanceServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ContentGovernanceModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/content-governance.php', 'content-governance');
        $this->app->singleton(ContentGovernanceService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('content-governance', 'Content Governance', AccessScope::Content, ['view', 'create', 'update', 'delete', 'hold']));
        }
    }
}
