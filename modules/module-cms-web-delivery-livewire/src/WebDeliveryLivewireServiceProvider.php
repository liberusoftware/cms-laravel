<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDeliveryLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class WebDeliveryLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-web-delivery-livewire');
        Livewire::addNamespace('module-cms-web-delivery', classNamespace: 'Liberu\\Cms\\WebDeliveryLivewire\\Livewire');
    }
}
