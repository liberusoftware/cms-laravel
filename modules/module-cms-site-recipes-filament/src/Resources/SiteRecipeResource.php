<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipesFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\SiteRecipes\Models\SiteRecipe;

final class SiteRecipeResource extends Resource
{
    protected static ?string $model = SiteRecipe::class;

    protected static ?string $slug = 'cms-site-recipes';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required()->alphaDash(), TextInput::make('name')->required(), TextInput::make('description')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key'), TextColumn::make('name'), TextColumn::make('status')->badge(), TextColumn::make('versions_count')->counts('versions')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListSiteRecipes::route('/')];
    }
}
