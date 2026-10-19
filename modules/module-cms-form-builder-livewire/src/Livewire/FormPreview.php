<?php

declare(strict_types=1);

namespace Liberu\Cms\FormBuilderLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\FormBuilder\Services\FormBuilderService;
use Livewire\Component;

final class FormPreview extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $steps = [];

    /** @var array<string, mixed> */
    public array $input = [];

    /** @var array<string, mixed> */
    public array $validated = [];

    public function submit(FormBuilderService $service): void
    {
        $this->validated = $service->validate($this->steps, $this->input);
    }

    public function render(FormBuilderService $service): View
    {
        return view('cms-form-builder-livewire::livewire.form-preview', ['fields' => $service->visibleFields($this->steps, $this->input)]);
    }
}
