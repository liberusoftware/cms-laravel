<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveysFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\PollsAndSurveysFilament\Resources\PollResource;

final class PollsAndSurveysFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('polls-and-surveys', PollResource::class);
        }
    }
}
