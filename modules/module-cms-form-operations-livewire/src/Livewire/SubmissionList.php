<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperationsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\FormOperations\Models\OperationalSubmission;
use Livewire\Component;

final class SubmissionList extends Component
{
    public string $formId = '';

    public function updatedFormId(): void
    {
        $this->formId = mb_substr(trim($this->formId), 0, 20);
    }

    public function render(): View
    {
        $submissions = OperationalSubmission::query()->when(ctype_digit($this->formId) && (int) $this->formId > 0, fn ($query) => $query->where('form_id', (int) $this->formId))->latest()->paginate(15);

        return view('cms-form-operations-livewire::livewire.submission-list', ['submissions' => $submissions]);
    }
}
