<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendar;

use Liberu\Cms\ContentCalendar\Services\ContentCalendarService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ContentCalendarServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ContentCalendarModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/content-calendar.php', 'content-calendar');
        $this->app->singleton(ContentCalendarService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('content-calendar', 'Content Calendar', AccessScope::Content, ['view', 'create', 'update', 'delete', 'reschedule']));
        }
    }
}
