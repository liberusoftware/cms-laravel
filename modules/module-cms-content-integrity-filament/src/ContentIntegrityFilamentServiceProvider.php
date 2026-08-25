<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrityFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentIntegrityFilament\Resources\IntegrityFindingResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContentIntegrityFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('content-integrity', IntegrityFindingResource::class);
        }
    }
}
