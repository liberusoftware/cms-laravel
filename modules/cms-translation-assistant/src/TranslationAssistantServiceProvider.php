<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistant;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\TranslationAssistant\Services\TranslationAssistantService;
use Liberu\Cms\TranslationAssistant\Queries\TranslationAssistantQuery;

final class TranslationAssistantServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface { return new TranslationAssistantModule; }
    protected function registerModule(): void { $this->app->singleton(TranslationAssistantService::class); $this->app->singleton(TranslationAssistantQuery::class); }
    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) { $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('translation-assistant', 'Translation Assistant', AccessScope::Module, ['view', 'create', 'update', 'delete', 'review'])); }
    }
}
