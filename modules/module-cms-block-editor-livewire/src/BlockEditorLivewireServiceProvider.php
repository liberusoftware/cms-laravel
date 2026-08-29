<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditorLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\BlockEditorLivewire\Livewire\BlockEditor;
use Livewire\Livewire;

final class BlockEditorLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-block-editor-livewire');
        Livewire::component('module-cms-block-editor.editor', BlockEditor::class);
    }
}
