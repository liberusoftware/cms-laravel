<?php

declare(strict_types=1);

namespace Liberu\Cms\LayoutBuilderFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\LayoutBuilderFilament\Resources\LayoutResource;

final class ListLayouts extends ListRecords
{
    protected static string $resource = LayoutResource::class;
}
