<?php

declare(strict_types=1);

namespace Liberu\Cms\Personalization;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Personalization\Services\DecisionEngine;
use Liberu\Cms\Personalization\Services\PersonalizationService;

final class PersonalizationServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new PersonalizationModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(DecisionEngine::class);
        $this->app->singleton(PersonalizationService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('personalization', 'Personalization', AccessScope::Content, ['view', 'create', 'update', 'delete']));
        }
    }
}
