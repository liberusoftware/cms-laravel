<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\Schemas\PageForm;
use App\Filament\Resources\Pages\Tables\PagesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Liberu\Cms\Pages\Models\Page;
use UnitEnum;

class PageResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Content';

    #[\Override]
    protected static ?string $model = Page::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    #[\Override]
    protected static ?string $recordTitleAttribute = 'title';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
