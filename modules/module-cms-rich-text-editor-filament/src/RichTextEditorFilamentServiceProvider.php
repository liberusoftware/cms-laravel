<?php

declare(strict_types=1);

namespace Liberu\Cms\RichTextEditorFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\RichTextEditorFilament\Resources\EditorResource;

final class RichTextEditorFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('rich-text-editor', EditorResource::class);
        }
    }
}
