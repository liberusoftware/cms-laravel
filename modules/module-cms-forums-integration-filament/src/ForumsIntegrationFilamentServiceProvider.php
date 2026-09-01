<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegrationFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\ForumsIntegrationFilament\Resources\ForumReferenceResource;

final class ForumsIntegrationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('forums-integration', ForumReferenceResource::class);
        }
    }
}
