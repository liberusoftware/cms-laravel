<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagementLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\DigitalAssetManagementLivewire\Livewire\AssetLibrary;
use Livewire\Livewire;

final class DigitalAssetManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-digital-asset-management::asset-library', AssetLibrary::class);
    }
}
