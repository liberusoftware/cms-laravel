<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLockingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentLockingLivewire\Livewire\LockPresence;
use Livewire\Livewire;

final class ContentLockingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-content-locking');
        Livewire::component('module-cms-content-locking::lock-presence', LockPresence::class);
    }
}
