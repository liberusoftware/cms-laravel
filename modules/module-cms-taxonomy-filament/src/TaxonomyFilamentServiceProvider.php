<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\TaxonomyFilament\Resources\TaxonomyResource;

final class TaxonomyFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('taxonomy', TaxonomyResource::class);
        }
    }
}
