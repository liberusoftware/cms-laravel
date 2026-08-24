<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgetsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\RegionsAndWidgets\Models\Region;

final class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $slug = 'cms-regions-and-widgets';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required()->alphaDash(), TextInput::make('label')->required(), TextInput::make('theme')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable(), TextColumn::make('label'), TextColumn::make('theme'), TextColumn::make('widgets_count')->counts('widgets')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListRegions::route('/')];
    }
}
