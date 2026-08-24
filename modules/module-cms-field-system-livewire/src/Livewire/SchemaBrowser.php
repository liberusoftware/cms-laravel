<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Livewire\Component;

final class SchemaBrowser extends Component
{
    public string $type = '';

    public function render(): View
    {
        return view('cms-field-system-livewire::livewire.schema-browser', [
            'contentType' => ContentType::query()->where('key', $this->type)->first(),
        ]);
    }
}
