<?php

namespace App\Filament\Resources\CollectionItems;

use App\Filament\Resources\CollectionItems\Pages\CreateCollectionItem;
use App\Filament\Resources\CollectionItems\Pages\EditCollectionItem;
use App\Filament\Resources\CollectionItems\Pages\ListCollectionItems;
use App\Filament\Resources\CollectionItems\Pages\ViewCollectionItem;
use App\Filament\Resources\CollectionItems\Schemas\CollectionItemForm;
use App\Filament\Resources\CollectionItems\Schemas\CollectionItemInfolist;
use App\Filament\Resources\CollectionItems\Tables\CollectionItemsTable;
use App\Models\CollectionItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CollectionItemResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Content';

    #[\Override]
    protected static ?string $model = CollectionItem::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    #[\Override]
    protected static ?string $modelLabel = 'Content';

    #[\Override]
    protected static ?string $pluralModelLabel = 'Content';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'title';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return CollectionItemForm::configure($schema);
    }

    #[\Override]
    public static function infolist(Schema $schema): Schema
    {
        return CollectionItemInfolist::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return CollectionItemsTable::configure($table);
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
            'index' => ListCollectionItems::route('/'),
            'create' => CreateCollectionItem::route('/create'),
            'view' => ViewCollectionItem::route('/{record}'),
            'edit' => EditCollectionItem::route('/{record}/edit'),
        ];
    }
}
