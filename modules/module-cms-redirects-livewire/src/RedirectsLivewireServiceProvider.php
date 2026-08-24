<?php

declare(strict_types=1);

namespace Liberu\Cms\RedirectsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\RedirectsLivewire\Livewire\RedirectLookup;
use Livewire\Livewire;

final class RedirectsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-redirects.redirect-lookup', RedirectLookup::class);
    }
}
