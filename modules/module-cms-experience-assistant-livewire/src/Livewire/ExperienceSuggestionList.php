<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistantLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ExperienceAssistant\Models\ExperienceSuggestion;
use Livewire\Component;

final class ExperienceSuggestionList extends Component
{
    public string $surface = '';

    public function updatedSurface(): void
    {
        $this->surface = mb_substr(trim($this->surface), 0, 180);
    }

    public function render(): View
    {
        $suggestions = ExperienceSuggestion::query()->when($this->surface !== '', fn ($query) => $query->where('surface', 'like', '%'.$this->surface.'%'))->latest()->paginate(15);

        return view('cms-experience-assistant-livewire::livewire.experience-suggestion-list', ['suggestions' => $suggestions]);
    }
}
