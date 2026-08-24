<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperationsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\SecurityOperationsLivewire\Livewire\OperationsDashboard;
use Livewire\Livewire;

final class SecurityOperationsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-security-operations.operations-dashboard', OperationsDashboard::class);
    }
}
