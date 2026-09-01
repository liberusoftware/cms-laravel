<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBaseFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\KnowledgeBaseFilament\Resources\KnowledgeArticleResource;

final class ListKnowledgeArticles extends ListRecords
{
    #[\Override]
    protected static string $resource = KnowledgeArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
