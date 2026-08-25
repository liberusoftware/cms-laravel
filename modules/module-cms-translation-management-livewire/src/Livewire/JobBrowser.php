<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\TranslationManagement\Actions\TranslationManagementService;
use Liberu\Cms\TranslationManagement\Queries\TranslationJobQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class JobBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public int $perPage = 15;

    public function updatedSearch(): void
    {
        $this->search = substr(trim($this->search), 0, 255);
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->status = substr(trim($this->status), 0, 64);
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = max(1, min(100, $this->perPage));
        $this->resetPage();
    }

    public function reconcile(string $publicId, TranslationJobQuery $jobs, TranslationManagementService $service): void
    {
        if ($job = $jobs->find($publicId)) {
            $service->reconcile($job);
        }
    }

    public function render(TranslationJobQuery $jobs): View
    {
        return view('module-cms-translation-management-livewire::livewire.job-browser', ['jobs' => $jobs->paginate($this->perPage, $this->search, $this->status ?: null)]);
    }
}
