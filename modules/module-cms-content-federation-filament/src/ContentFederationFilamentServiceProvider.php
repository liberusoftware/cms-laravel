<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederationFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentFederationFilament\Resources\FederationSourceResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContentFederationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('content-federation', FederationSourceResource::class);
        }
    }
}
