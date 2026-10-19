<?php

declare(strict_types=1);

namespace Liberu\Cms\LocalizationFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\LocalizationFilament\Resources\LocaleResource;

final class LocalizationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('localization', LocaleResource::class);
        }
    }
}
