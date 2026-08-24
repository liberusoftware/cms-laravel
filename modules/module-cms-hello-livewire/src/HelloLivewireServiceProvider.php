<?php

declare(strict_types=1);

namespace Liberu\Cms\HelloLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\HelloLivewire\Livewire\GreetingList;
use Livewire\Livewire;

final class HelloLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-hello-livewire');

        Livewire::component('cms-hello-livewire.greeting-list', GreetingList::class);
    }
}
