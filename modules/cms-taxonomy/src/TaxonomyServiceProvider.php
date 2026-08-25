<?php

declare(strict_types=1);

namespace Liberu\Cms\Taxonomy;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Taxonomy\Queries\TaxonomyQuery;
use Liberu\Cms\Taxonomy\Services\TaxonomyService;

final class TaxonomyServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new TaxonomyModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(TaxonomyService::class);
        $this->app->singleton(TaxonomyQuery::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('taxonomy', 'Taxonomy', AccessScope::Module, ['view', 'create', 'update', 'delete']));
        }
    }
}
