<?php

declare(strict_types=1);

namespace Liberu\Cms\LocalizationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\LocalizationFilament\Resources\LocaleResource;

final class ListLocales extends ListRecords
{
    protected static string $resource = LocaleResource::class;
}
