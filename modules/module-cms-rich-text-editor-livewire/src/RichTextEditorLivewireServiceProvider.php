<?php

declare(strict_types=1);

namespace Liberu\Cms\RichTextEditorLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\RichTextEditorLivewire\Livewire\Editor;
use Livewire\Livewire;

final class RichTextEditorLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-rich-text-editor.editor', Editor::class);
    }
}
