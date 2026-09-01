<?php

declare(strict_types=1);

namespace Liberu\Cms\AuditAndHistoryLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AuditAndHistoryLivewire\Livewire\AuditHistory;
use Livewire\Livewire;

final class AuditAndHistoryLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-audit-and-history-livewire');
        Livewire::component('module-cms-audit-and-history.history', AuditHistory::class);
    }
}
