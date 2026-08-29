<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystem;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\FieldSystem\Services\FieldSystemService;

final class FieldSystemServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new FieldSystemModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(FieldSystemService::class, fn ($app): FieldSystemService => new FieldSystemService($app->make(FieldTypeRegistryInterface::class)));
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('field-system', 'Field System', AccessScope::Content, ['view', 'create', 'update', 'validate', 'migrate']));
        }
    }
}
