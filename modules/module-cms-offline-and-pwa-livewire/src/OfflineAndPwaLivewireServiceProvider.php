<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwaLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\OfflineAndPwaLivewire\Livewire\PwaStatus;
use Livewire\Livewire;

final class OfflineAndPwaLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-offline-and-pwa.pwa-status', PwaStatus::class);
    }
}
