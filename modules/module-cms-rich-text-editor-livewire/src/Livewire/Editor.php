<?php

declare(strict_types=1);

namespace Liberu\Cms\RichTextEditorLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\RichTextEditor\Services\RichTextService;
use Livewire\Component;

final class Editor extends Component
{
    public string $content = '';

    public array $hints = [];

    public function updatedContent(RichTextService $service): void
    {
        $this->hints = $service->accessibilityHints($this->content);
    }

    public function render(): View
    {
        return view('cms-rich-text-editor-livewire::editor');
    }
}
