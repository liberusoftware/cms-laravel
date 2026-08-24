<?php

declare(strict_types=1);

namespace Liberu\Cms\RelatedContent;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\RelatedContent\Services\RelatedContentService;

final class RelatedContentServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new RelatedContentModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(RelatedContentService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('related-content', 'Related Content', AccessScope::Content, ['view', 'create', 'update', 'delete', 'exclude']));
        }
    }
}
