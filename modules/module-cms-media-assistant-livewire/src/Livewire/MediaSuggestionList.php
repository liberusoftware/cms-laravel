<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistantLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\MediaAssistant\Models\MediaSuggestion;
use Livewire\Component;

final class MediaSuggestionList extends Component
{
    public string $assetKey = '';

    public function updatedAssetKey(): void
    {
        $this->assetKey = mb_substr(trim($this->assetKey), 0, 500);
    }

    public function render(): View
    {
        $suggestions = MediaSuggestion::query()->when($this->assetKey !== '', fn ($query) => $query->where('asset_key', $this->assetKey))->latest()->paginate(15);

        return view('cms-media-assistant-livewire::livewire.media-suggestion-list', ['suggestions' => $suggestions]);
    }
}
