<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernanceFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentGovernanceFilament\Resources\GovernanceRecordResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContentGovernanceFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('content-governance', GovernanceRecordResource::class);
        }
    }
}
