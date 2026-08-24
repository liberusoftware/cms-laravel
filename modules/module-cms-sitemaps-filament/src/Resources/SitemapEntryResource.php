<?php

declare(strict_types=1);

namespace Liberu\Cms\SitemapsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Sitemaps\Models\SitemapEntry;

final class SitemapEntryResource extends Resource
{
    protected static ?string $model = SitemapEntry::class;

    protected static ?string $slug = 'cms-sitemaps';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('url')->url()->required(), TextInput::make('type')->required(), TextInput::make('locale'), TextInput::make('priority')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('url')->searchable(), TextColumn::make('type'), TextColumn::make('locale'), TextColumn::make('priority'), TextColumn::make('excluded')->badge()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListSitemapEntries::route('/')];
    }
}
