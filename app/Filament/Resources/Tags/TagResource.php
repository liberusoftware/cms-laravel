<?php

namespace App\Filament\Resources\Tags;

use App\Filament\Resources\Tags\Pages\CreateTag;
use App\Filament\Resources\Tags\Pages\EditTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Filament\Resources\Tags\Schemas\TagForm;
use App\Filament\Resources\Tags\Tables\TagsTable;
use App\Models\Tag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TagResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Structure';

    #[\Override]
    protected static ?string $model = Tag::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    #[\Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return TagForm::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
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
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }
}
