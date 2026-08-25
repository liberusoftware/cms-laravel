<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\TranslationManagement\Actions\TranslationManagementService;
use Liberu\Cms\TranslationManagementFilament\Resources\TranslationJobResource;

final class ListTranslationJobs extends ListRecords
{
    #[\Override]
    protected static string $resource = TranslationJobResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->using(fn (array $data) => app(TranslationManagementService::class)->createJob($data))];
    }
}
