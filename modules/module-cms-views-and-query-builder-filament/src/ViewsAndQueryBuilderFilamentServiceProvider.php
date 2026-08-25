<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilderFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\ViewsAndQueryBuilderFilament\Resources\ViewDefinitionResource;

final class ViewsAndQueryBuilderFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('views-and-query-builder', ViewDefinitionResource::class);
        }
    }
}
