<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditorLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\BlockEditor\Services\BlockEditorService;
use Livewire\Component;

final class BlockEditor extends Component
{
    public string $subjectType = 'page';

    public string $subjectId = '';

    public array $blocks = [];

    public ?int $version = null;

    public string $status = 'Ready';

    public function save(BlockEditorService $service): void
    {
        $document = $service->save(null, $this->subjectType, $this->subjectId, $this->blocks, $this->version);
        $this->version = $document->version;
        $this->status = 'Saved';
    }

    public function render(): View
    {
        return view('module-cms-block-editor-livewire::editor');
    }
}
