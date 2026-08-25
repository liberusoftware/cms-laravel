<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveysLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\PollsAndSurveysLivewire\Livewire\PollForm;
use Livewire\Livewire;

final class PollsAndSurveysLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-polls-and-surveys.poll-form', PollForm::class);
    }
}
