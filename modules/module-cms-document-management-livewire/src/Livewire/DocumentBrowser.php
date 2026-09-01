<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagementLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\DocumentManagement\Services\DocumentManagementService;
use Livewire\Component;

final class DocumentBrowser extends Component
{
    public function render(): View
    {
        return view('module-cms-document-management::document-browser', ['documents' => app(DocumentManagementService::class)->documents(auth()->user()?->current_team_id)]);
    }
}
