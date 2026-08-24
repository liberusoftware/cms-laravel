<?php

declare(strict_types=1);

namespace Liberu\Cms\SeoLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\SeoLivewire\Livewire\SeoCheck;
use Livewire\Livewire;

final class SeoLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-seo.seo-check', SeoCheck::class);
    }
}
