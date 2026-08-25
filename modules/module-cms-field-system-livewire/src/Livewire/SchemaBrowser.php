<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ContentTypes\Queries\FieldSchemaQuery;
use Livewire\Component;

final class SchemaBrowser extends Component
{
    public string $type = '';

    private FieldSchemaQuery $schemas;

    public function boot(FieldSchemaQuery $schemas): void
    {
        $this->schemas = $schemas;
    }

    public function render(): View
    {
        return view('cms-field-system-livewire::livewire.schema-browser', [
            'schema' => preg_match('/^[a-z0-9][a-z0-9_-]{0,254}$/', $this->type)
                ? $this->schemas->find($this->type)
                : null,
        ]);
    }
}
