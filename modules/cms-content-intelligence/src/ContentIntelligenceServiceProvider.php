<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligence;

use Liberu\Cms\ContentIntelligence\Services\ContentIntelligenceService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ContentIntelligenceServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ContentIntelligenceModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/content-intelligence.php', 'content-intelligence');
        $this->app->singleton(ContentIntelligenceService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('content-intelligence', 'Content Intelligence', AccessScope::Content, ['view', 'analyze', 'review']));
        }
    }
}
