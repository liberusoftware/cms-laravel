<?php

declare(strict_types=1);

namespace Liberu\Cms\RedirectsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\RedirectsFilament\Resources\RedirectResource;

final class ListRedirects extends ListRecords
{
    #[\Override]
    protected static string $resource = RedirectResource::class;
}
