<?php

declare(strict_types=1);

namespace Liberu\Cms\RecommendationsFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Recommendations\Models\RecommendationList;

final class RecommendationListResource extends Resource
{
    protected static ?string $model = RecommendationList::class;

    protected static ?string $slug = 'cms-recommendations';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), TextInput::make('key')->required()->alphaDash(), Select::make('kind')->options(['latest' => 'Latest', 'popular' => 'Popular', 'trending' => 'Trending', 'editorial' => 'Editorial'])->required(), TextInput::make('ranker')->default('default')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('key'), TextColumn::make('kind')->badge(), TextColumn::make('items_count')->counts('items')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListRecommendationLists::route('/')];
    }
}
