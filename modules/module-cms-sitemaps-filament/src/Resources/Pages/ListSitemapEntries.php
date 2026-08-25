<?php

declare(strict_types=1);

namespace Liberu\Cms\SitemapsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\SitemapsFilament\Resources\SitemapEntryResource;

final class ListSitemapEntries extends ListRecords
{
    #[\Override]
    protected static string $resource = SitemapEntryResource::class;
}
