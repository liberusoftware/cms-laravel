<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflowFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\EditorialWorkflowFilament\Resources\EditorialWorkflowResource;

final class EditorialWorkflowFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('editorial-workflow', EditorialWorkflowResource::class);
        }
    }
}
