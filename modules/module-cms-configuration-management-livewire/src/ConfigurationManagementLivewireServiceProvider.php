<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagementLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ConfigurationManagementLivewire\Livewire\ConfigurationReleases;
use Livewire\Livewire;

final class ConfigurationManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-configuration-management.configuration-releases', ConfigurationReleases::class);
    }
}
