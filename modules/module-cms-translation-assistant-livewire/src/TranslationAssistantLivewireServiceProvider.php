<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistantLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\TranslationAssistantLivewire\Livewire\DraftList;
use Livewire\Livewire;

final class TranslationAssistantLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void { Livewire::addNamespace('module-cms-translation-assistant', classNamespace: 'Liberu\\Cms\\TranslationAssistantLivewire\\Livewire'); Livewire::component('module-cms-translation-assistant::draft-list', DraftList::class); }
}
