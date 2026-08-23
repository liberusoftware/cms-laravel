<?php

declare(strict_types=1);

namespace Liberu\Cms\Users;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Liberu\Cms\Contracts\Access\AccessControlInterface;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Users\Access\AccessControl;
use Liberu\Cms\Users\Access\PermissionRegistrar;
use Liberu\Cms\Users\Access\SyncPermissions;
use Liberu\Cms\Users\Console\SyncPermissionsCommand;

final class UsersServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new UsersModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/users.php', 'cms-users');

        $this->app->singleton(PermissionRegistrarInterface::class, PermissionRegistrar::class);

        $this->app->singleton(AccessControlInterface::class, fn (): AccessControl => new AccessControl(
            $this->app->make(AuthFactory::class),
            $this->app->make(Gate::class),
        ));

        $this->app->bind(SyncPermissions::class, fn (): SyncPermissions => new SyncPermissions(
            $this->app->make(PermissionRegistrarInterface::class),
            $this->guard(),
        ));
    }

    protected function bootModule(): void
    {
        $this->declarePermissions($this->app->make(PermissionRegistrarInterface::class));

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/users.php' => $this->app->configPath('cms-users.php'),
            ], 'cms-users-config');

            $this->commands([SyncPermissionsCommand::class]);
        }
    }

    private function declarePermissions(PermissionRegistrarInterface $registrar): void
    {
        $registrar->register(new PermissionGroup('users', 'Users', AccessScope::Module, [
            'view', 'create', 'update', 'delete',
        ]));

        $registrar->register(new PermissionGroup('roles', 'Roles', AccessScope::Module, [
            'view', 'create', 'update', 'delete', 'assign',
        ]));
    }

    private function guard(): string
    {
        $guard = config('cms-users.guard', 'web');

        return is_string($guard) ? $guard : 'web';
    }
}
