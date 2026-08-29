<?php

declare(strict_types=1);

namespace Liberu\Cms\MultisiteFilament\Resources\MultisiteSiteResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\MultisiteFilament\Resources\MultisiteSiteResource;

final class ListSites extends ListRecords
{
    #[\Override]
    protected static string $resource = MultisiteSiteResource::class;
}
