<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContent;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\EventsContent\Queries\EventsContentQuery;
use Liberu\Cms\EventsContent\Services\EventsContentService;

final class EventsContentServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new EventsContentModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(EventsContentService::class);
        $this->app->singleton(EventsContentQuery::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('events-content', 'Events Content', AccessScope::Module, ['view', 'create', 'update', 'delete', 'publish', 'archive']));
        }
    }
}
