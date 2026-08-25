<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendarFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentCalendarFilament\Resources\CalendarItemResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContentCalendarFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('content-calendar', CalendarItemResource::class);
        }
    }
}
