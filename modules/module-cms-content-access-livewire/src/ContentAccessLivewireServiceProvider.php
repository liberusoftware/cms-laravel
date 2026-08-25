<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccessLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentAccessLivewire\Livewire\AccessChecker;
use Livewire\Livewire;

final class ContentAccessLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-content-access::access-checker', AccessChecker::class);
    }
}
