<?php

declare(strict_types=1);

namespace Liberu\Cms\RevisionsFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\RevisionsFilament\Resources\RevisionResource;

final class RevisionsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('revisions', RevisionResource::class);
        }
    }
}
