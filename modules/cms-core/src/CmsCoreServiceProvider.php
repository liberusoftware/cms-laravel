<?php

declare(strict_types=1);

namespace Liberu\Cms\Core;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Contracts\Module\ModuleManagerInterface;
use Liberu\Cms\Contracts\Module\ModuleRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleStateRepositoryInterface;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Liberu\Cms\Contracts\Tenancy\TenantModelResolverInterface;
use Liberu\Cms\Core\Actions\CoreMutationService;
use Liberu\Cms\Core\Console\MakeBlockCommand;
use Liberu\Cms\Core\Console\MakeFieldTypeCommand;
use Liberu\Cms\Core\Console\MakeHookCommand;
use Liberu\Cms\Core\Console\MakeModuleCommand;
use Liberu\Cms\Core\Events\EventBus;
use Liberu\Cms\Core\Hooks\HookBus;
use Liberu\Cms\Core\Module\DatabaseModuleStateRepository;
use Liberu\Cms\Core\Module\ModuleManager;
use Liberu\Cms\Core\Module\ModuleRegistry;
use Liberu\Cms\Core\Tenant\NullTenantResolver;
use Liberu\Cms\Core\Tenant\RequestTenantContext;

/**
 * The CMS kernel provider.
 *
 * Registers the registry, state repository, manager, and event bus as
 * singletons so every module and the host application resolve the same
 * instances. It is registered before module providers interact with these
 * services because Laravel completes all register() calls before any boot().
 */
final class CmsCoreServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cms.php', 'cms');

        $this->app->singleton(CoreMutationService::class);

        $this->app->singleton(ModuleRegistryInterface::class, ModuleRegistry::class);

        $this->app->singleton(ModuleStateRepositoryInterface::class, fn (): DatabaseModuleStateRepository => new DatabaseModuleStateRepository($this->app->make(ConnectionResolverInterface::class)));

        $this->app->singleton(ModuleManagerInterface::class, function (): ModuleManager {
            $config = $this->app->make(ConfigRepository::class);

            return new ModuleManager(
                registry: $this->app->make(ModuleRegistryInterface::class),
                state: $this->app->make(ModuleStateRepositoryInterface::class),
                enabledByDefault: (bool) $config->get('cms.modules_enabled_by_default', true),
                forcedDisabled: $this->disabledModules($config),
            );
        });

        $this->app->singleton(EventBusInterface::class, fn (): EventBus => new EventBus($this->app));

        $this->app->singleton(HookBusInterface::class, fn (): HookBus => new HookBus($this->app));

        $this->app->bindIf(TenantModelResolverInterface::class, NullTenantResolver::class);

        $this->app->singleton(TenantContextInterface::class, RequestTenantContext::class);
    }

    /**
     * @return array<int, string>
     */
    private function disabledModules(ConfigRepository $config): array
    {
        $disabled = $config->get('cms.disabled_modules', []);

        if (! is_array($disabled)) {
            return [];
        }

        return array_values(array_filter($disabled, is_string(...)));
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(
                new PermissionGroup('core', 'CMS Core', AccessScope::Module, ['view', 'create', 'update', 'delete']),
            );
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cms.php' => $this->app->configPath('cms.php'),
            ], 'cms-config');

            $this->commands([
                MakeModuleCommand::class,
                MakeBlockCommand::class,
                MakeHookCommand::class,
                MakeFieldTypeCommand::class,
            ]);
        }
    }
}
