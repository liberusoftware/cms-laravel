<?php

declare(strict_types=1);

namespace Liberu\Cms\LayoutBuilderLivewire\Livewire;

use Liberu\Cms\LayoutBuilder\Models\Layout;
use Livewire\Component;

final class LayoutBrowser extends Component
{
    public string $targetType = '';

    public string $targetId = '';

    public function render(): mixed
    {
        return view('module-cms-layout-builder::layout-browser', ['layout' => $this->targetType !== '' && $this->targetId !== '' ? Layout::query()->published()->where('target_type', $this->targetType)->where('target_id', $this->targetId)->first() : null]);
    }
}
