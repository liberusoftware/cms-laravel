<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistantFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\TranslationAssistantFilament\Resources\TranslationDraftResource;

final class ListTranslationDrafts extends ListRecords
{
    protected static string $resource = TranslationDraftResource::class;
}
