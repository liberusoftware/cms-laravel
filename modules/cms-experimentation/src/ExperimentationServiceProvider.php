<?php

declare(strict_types=1);

namespace Liberu\Cms\Experimentation;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Experimentation\Queries\ExperimentationQuery;
use Liberu\Cms\Experimentation\Services\ExperimentationService;

final class ExperimentationServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ExperimentationModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(ExperimentationService::class);
        $this->app->singleton(ExperimentationQuery::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('experimentation', 'Experimentation', AccessScope::Module, ['view', 'create', 'update', 'delete', 'start', 'stop', 'promote']));
        }
    }
}
