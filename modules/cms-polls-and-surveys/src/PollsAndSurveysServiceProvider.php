<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveys;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\PollsAndSurveys\Services\PollService;

final class PollsAndSurveysServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new PollsAndSurveysModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(PollService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('polls-and-surveys', 'Polls and Surveys', AccessScope::Content, ['view', 'create', 'update', 'delete', 'export']));
        }
    }
}
