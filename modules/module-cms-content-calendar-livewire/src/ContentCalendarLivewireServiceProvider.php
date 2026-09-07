<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendarLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentCalendarLivewire\Livewire\CalendarBoard;
use Livewire\Livewire;

final class ContentCalendarLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-content-calendar');
        Livewire::component('module-cms-content-calendar::calendar-board', CalendarBoard::class);
    }
}
