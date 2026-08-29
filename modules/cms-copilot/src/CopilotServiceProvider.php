<?php

declare(strict_types=1);

namespace Liberu\Cms\Copilot;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Copilot\Services\CopilotService;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class CopilotServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new CopilotModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(CopilotService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('cms-copilot', 'CMS Copilot', AccessScope::Content, ['view', 'execute', 'confirm']));
        }
    }
}
