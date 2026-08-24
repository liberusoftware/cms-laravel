<?php

declare(strict_types=1);

namespace Liberu\Cms\RevisionsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Content\Revisions\Revision;
use Livewire\Component;

final class RevisionHistory extends Component
{
    public string $revisionableType = '';

    public int $revisionableId = 0;

    public function render(): View
    {
        return view('cms-revisions-livewire::revision-history', ['revisions' => $this->revisionableType === '' || $this->revisionableId === 0 ? [] : Revision::query()->where('revisionable_type', $this->revisionableType)->where('revisionable_id', $this->revisionableId)->latest('revision_number')->limit(20)->get()]);
    }
}
