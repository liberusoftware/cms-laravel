<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditor;

use Liberu\Cms\BlockEditor\Services\BlockEditorService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class BlockEditorServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new BlockEditorModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(BlockEditorService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('block-editor', 'Block Editor', AccessScope::Content, ['view', 'create', 'update', 'lock', 'preview']));
        }
    }
}
