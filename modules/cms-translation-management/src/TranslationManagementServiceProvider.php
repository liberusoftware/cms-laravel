<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\TranslationManagement\Actions\TranslationManagementService;
use Liberu\Cms\TranslationManagement\Queries\TranslationJobQuery;
use Liberu\Cms\TranslationManagement\Support\TranslationVendorRegistry;

final class TranslationManagementServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface { return new TranslationManagementModule; }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/translation-management.php', 'translation-management');
        $this->app->singleton(TranslationVendorRegistry::class);
        $this->app->singleton(TranslationManagementService::class);
        $this->app->singleton(TranslationJobQuery::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('translation-management', 'Translation Management', AccessScope::Content, ['view', 'create', 'update', 'delete', 'translate', 'review', 'reconcile']));
        }
    }
}
