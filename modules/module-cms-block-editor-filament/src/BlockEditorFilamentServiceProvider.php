<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditorFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\BlockEditorFilament\Resources\BlockDocumentResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class BlockEditorFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('block-editor', BlockDocumentResource::class);
        }
    }
}
