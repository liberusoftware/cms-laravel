<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Categories\Tables\CategoriesTable;
use App\Models\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Structure';

    #[\Override]
    protected static ?string $model = Category::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    #[\Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
