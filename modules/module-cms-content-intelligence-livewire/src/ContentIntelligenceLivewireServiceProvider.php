<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligenceLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentIntelligenceLivewire\Livewire\InsightQueue;
use Livewire\Livewire;

final class ContentIntelligenceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-content-intelligence::insight-queue', InsightQueue::class);
    }
}
