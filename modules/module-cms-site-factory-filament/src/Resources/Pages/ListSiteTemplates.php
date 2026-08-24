<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactoryFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\SiteFactoryFilament\Resources\SiteTemplateResource;

final class ListSiteTemplates extends ListRecords
{
    protected static string $resource = SiteTemplateResource::class;
}
