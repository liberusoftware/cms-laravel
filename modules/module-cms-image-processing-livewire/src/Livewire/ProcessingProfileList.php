<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessingLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ImageProcessing\Models\ProcessingProfile;
use Livewire\Component;

final class ProcessingProfileList extends Component
{
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->search = mb_substr(trim($this->search), 0, 120);
    }

    public function render(): View
    {
        $profiles = ProcessingProfile::query()->when($this->search !== '', fn ($query) => $query->where('key', 'like', '%'.$this->search.'%'))->latest()->paginate(15);

        return view('cms-image-processing-livewire::livewire.processing-profile-list', ['profiles' => $profiles]);
    }
}
