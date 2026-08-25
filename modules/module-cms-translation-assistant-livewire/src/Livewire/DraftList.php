<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistantLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\TranslationAssistant\Models\TranslationDraft;
use Livewire\Component;

final class DraftList extends Component
{
    public string $targetLocale = '';
    public function render(): View { return view('cms-translation-assistant-livewire::draft-list', ['drafts' => TranslationDraft::query()->where('team_id', null)->when($this->targetLocale !== '', fn ($q) => $q->where('target_locale', $this->targetLocale))->latest()->get()]); }
}
