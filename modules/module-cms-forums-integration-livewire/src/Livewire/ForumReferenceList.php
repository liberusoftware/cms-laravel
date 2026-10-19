<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegrationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ForumsIntegration\Models\ForumReference;
use Livewire\Component;

final class ForumReferenceList extends Component
{
    public string $provider = '';

    public function updatedProvider(): void
    {
        $this->provider = mb_substr(trim($this->provider), 0, 180);
    }

    public function render(): View
    {
        $references = ForumReference::query()->when($this->provider !== '', fn ($query) => $query->where('provider', $this->provider))->latest()->paginate(15);

        return view('cms-forums-integration-livewire::livewire.forum-reference-list', ['references' => $references]);
    }
}
