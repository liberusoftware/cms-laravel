<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilderFilament\Resources;

use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ViewsAndQueryBuilder\Models\ViewDefinition;
use UnitEnum;

final class ViewDefinitionResource extends Resource
{
    protected static ?string $model = ViewDefinition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->maxLength(255),
            Select::make('source')->options(config('views-and-query-builder.sources', []))->required(),
            Textarea::make('description')->maxLength(1000),
            KeyValue::make('definition')->required(),
            Select::make('visibility')->options(array_combine(config('views-and-query-builder.allowed_visibility', []), config('views-and-query-builder.allowed_visibility', [])))->required(),
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('source')->badge(),
            TextColumn::make('visibility')->badge(),
            TextColumn::make('status')->badge(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('name');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListViewDefinitions::route('/')];
    }
}
