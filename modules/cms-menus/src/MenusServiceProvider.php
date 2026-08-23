<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Admin\AdminDashboardRegistryInterface;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Admin\DashboardStat;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Menus\Contracts\MenuRepositoryInterface;
use Liberu\Cms\Menus\Filament\MenuItemResource;
use Liberu\Cms\Menus\Filament\MenuResource;
use Liberu\Cms\Menus\Http\Controllers\MenuApiController;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\Menus\Repositories\MenuRepository;

final class MenusServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new MenusModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(MenuRepositoryInterface::class, MenuRepository::class);

        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $registry = $this->app->make(AdminResourceRegistryInterface::class);
            $registry->registerResource('menus', MenuResource::class);
            $registry->registerResource('menus', MenuItemResource::class);
        }

        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint(
                'menus',
                new ApiEndpoint('menus/{location}', MenuApiController::class, 'show', 'menus.show'),
            );
        }
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');

        if ($this->app->bound(AdminDashboardRegistryInterface::class)) {
            $this->app->make(AdminDashboardRegistryInterface::class)->registerStat(
                new DashboardStat('Menus', fn (): int => Menu::count(), 'heroicon-o-bars-3', 'primary'),
            );
        }

        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(
                new PermissionGroup('menus', 'Menus', AccessScope::Content, ['view', 'create', 'update', 'delete']),
            );
        }
    }
}
