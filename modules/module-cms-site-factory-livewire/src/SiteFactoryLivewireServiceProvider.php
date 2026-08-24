<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactoryLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\SiteFactoryLivewire\Livewire\SiteProvisioner;
use Livewire\Livewire;

final class SiteFactoryLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-site-factory.site-provisioner', SiteProvisioner::class);
    }
}
