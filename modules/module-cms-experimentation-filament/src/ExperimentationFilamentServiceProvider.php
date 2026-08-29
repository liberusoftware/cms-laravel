<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperimentationFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\ExperimentationFilament\Resources\ExperimentResource;

final class ExperimentationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('experimentation', ExperimentResource::class);
        }
    }
}
