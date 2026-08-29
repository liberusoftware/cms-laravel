<?php

declare(strict_types=1);

namespace Liberu\Cms\MetadataLivewire\Livewire;

use Liberu\Cms\Metadata\Services\MetadataService;
use Livewire\Component;

final class MetadataBrowser extends Component
{
    public string $subjectType = '';

    public string $subjectId = '';

    public function render(MetadataService $metadata): mixed
    {
        $values = $this->subjectType !== '' && $this->subjectId !== '' ? $metadata->all($this->subjectType, $this->subjectId) : [];

        return view('module-cms-metadata::metadata-browser', ['metadata' => $values]);
    }
}
