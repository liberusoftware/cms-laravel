<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\TaxonomyFilament\Resources\TaxonomyResource;

final class ListTaxonomies extends ListRecords
{
    #[\Override]
    protected static string $resource = TaxonomyResource::class;
}
