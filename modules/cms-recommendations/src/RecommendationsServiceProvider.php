<?php

declare(strict_types=1);

namespace Liberu\Cms\Recommendations;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Recommendations\Contracts\Ranker;
use Liberu\Cms\Recommendations\Services\DefaultRanker;
use Liberu\Cms\Recommendations\Services\RecommendationService;

final class RecommendationsServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new RecommendationsModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(Ranker::class, DefaultRanker::class);
        $this->app->singleton(RecommendationService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('recommendations', 'Recommendations', AccessScope::Content, ['view', 'create', 'update', 'delete']));
        }
    }
}
