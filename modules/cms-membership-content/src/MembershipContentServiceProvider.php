<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContent;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\MembershipContent\Services\MembershipContentService;

final class MembershipContentServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new MembershipContentModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(MembershipContentService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('membership-content', 'Membership Content', AccessScope::Content, ['view', 'create', 'update', 'delete', 'grant', 'download']));
        }
    }
}
