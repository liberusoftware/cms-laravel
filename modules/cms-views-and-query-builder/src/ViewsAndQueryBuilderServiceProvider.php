<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilder;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\ViewsAndQueryBuilder\Actions\ViewDefinitionMutationService;
use Liberu\Cms\ViewsAndQueryBuilder\Queries\ListingQueryService;
use Liberu\Cms\ViewsAndQueryBuilder\Queries\ViewDefinitionQuery;

final class ViewsAndQueryBuilderServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ViewsAndQueryBuilderModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/views-and-query-builder.php', 'views-and-query-builder');
        $this->app->singleton(ViewDefinitionQuery::class);
        $this->app->singleton(ListingQueryService::class);
        $this->app->singleton(ViewDefinitionMutationService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('views-and-query-builder', 'Views and Query Builder', AccessScope::Content, ['view', 'create', 'update', 'delete', 'execute']));
        }
    }
}
