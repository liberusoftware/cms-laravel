<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeeds;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\SyndicationAndFeeds\Services\FeedService;

final class SyndicationAndFeedsServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new SyndicationAndFeedsModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(FeedService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('syndication-and-feeds', 'Syndication and Feeds', AccessScope::Module, ['view', 'create', 'import', 'syndicate', 'update', 'delete']));
        }
    }
}
