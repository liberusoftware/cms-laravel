<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipesFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\SiteRecipesFilament\Resources\SiteRecipeResource;

final class ListSiteRecipes extends ListRecords
{
    protected static string $resource = SiteRecipeResource::class;
}
