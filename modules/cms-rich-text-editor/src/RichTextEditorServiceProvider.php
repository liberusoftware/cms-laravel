<?php

declare(strict_types=1);

namespace Liberu\Cms\RichTextEditor;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\RichTextEditor\Services\RichTextService;

final class RichTextEditorServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new RichTextEditorModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(RichTextService::class);
    }

    protected function bootModule(): void
    {
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('rich-text-editor', 'Rich Text Editor', AccessScope::Content, ['view', 'create', 'update', 'sanitize', 'embed']));
        }
    }
}
