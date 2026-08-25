<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigrationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\WordPressMigration\Models\WordPressMigration;
use Livewire\Component;

final class MigrationList extends Component
{
    public string $status = '';
    public function render(): View { return view('cms-wordpress-migration-livewire::migration-list', ['migrations' => WordPressMigration::query()->when($this->status !== '', fn ($q) => $q->where('status', $this->status))->latest()->get()]); }
}
