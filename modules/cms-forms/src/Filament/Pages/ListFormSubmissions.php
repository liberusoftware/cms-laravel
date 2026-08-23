<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Filament\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Forms\Filament\FormSubmissionResource;

final class ListFormSubmissions extends ListRecords
{
    protected static string $resource = FormSubmissionResource::class;
}
