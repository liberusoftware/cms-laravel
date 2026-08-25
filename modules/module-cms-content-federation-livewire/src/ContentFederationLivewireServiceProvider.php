<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentFederationLivewire\Livewire\SourceHealth;
use Livewire\Livewire;

final class ContentFederationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-content-federation::source-health', SourceHealth::class);
    }
}
