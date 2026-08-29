<?php

namespace Liberu\Cms\EmbedsFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\EmbedsFilament\Resources\EmbedResource;

class EmbedsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('embeds', EmbedResource::class);
        }
    }
}
