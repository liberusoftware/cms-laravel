<?php

declare(strict_types=1);

namespace Liberu\Cms\RecommendationsFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\RecommendationsFilament\Resources\RecommendationListResource;

final class ListRecommendationLists extends ListRecords
{
    protected static string $resource = RecommendationListResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
