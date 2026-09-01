<?php

namespace App\Filament\Resources\Collections;

use App\Filament\Resources\Collections\Pages\CreateCollection;
use App\Filament\Resources\Collections\Pages\EditCollection;
use App\Filament\Resources\Collections\Pages\ListCollections;
use App\Filament\Resources\Collections\Schemas\CollectionForm;
use App\Filament\Resources\Collections\Tables\CollectionsTable;
use App\Models\Collection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CollectionResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Content';

    #[\Override]
    protected static ?string $model = Collection::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    #[\Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return CollectionForm::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return CollectionsTable::configure($table);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListCollections::route('/'),
            'create' => CreateCollection::route('/create'),
            'edit' => EditCollection::route('/{record}/edit'),
        ];
    }
}
