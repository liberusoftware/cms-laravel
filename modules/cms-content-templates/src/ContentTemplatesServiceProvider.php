<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplates;

use Liberu\Cms\ContentTemplates\Services\ContentTemplatesService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ContentTemplatesServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ContentTemplatesModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/content-templates.php', 'content-templates');
        $this->app->singleton(ContentTemplatesService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('content-templates', 'Content Templates', AccessScope::Content, ['view', 'create', 'update', 'publish', 'lock']));
        }
    }
}
