<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagementFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\DocumentManagementFilament\Resources\DocumentResource;

final class DocumentManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('documents', DocumentResource::class);
        }
    }
}
