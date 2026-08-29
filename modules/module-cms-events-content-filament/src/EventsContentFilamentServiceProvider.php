<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContentFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\EventsContentFilament\Resources\EventResource;

final class EventsContentFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('events-content', EventResource::class);
        }
    }
}
