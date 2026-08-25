<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilderFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ViewsAndQueryBuilderFilament\Resources\ViewDefinitionResource;

final class ListViewDefinitions extends ListRecords
{
    protected static string $resource = ViewDefinitionResource::class;
}
