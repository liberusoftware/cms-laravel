<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernanceLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentGovernanceLivewire\Livewire\GovernanceOverview;
use Livewire\Livewire;

final class ContentGovernanceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-content-governance::governance-overview', GovernanceOverview::class);
    }
}
