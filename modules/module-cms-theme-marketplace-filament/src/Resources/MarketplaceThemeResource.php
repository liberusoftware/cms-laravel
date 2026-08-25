<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ThemeMarketplace\Models\MarketplaceTheme;

final class MarketplaceThemeResource extends Resource
{
    #[\Override]
    protected static ?string $model = MarketplaceTheme::class;

    #[\Override]
    protected static ?string $slug = 'cms-theme-marketplace';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required(), TextInput::make('name')->required(), TextInput::make('version')->required(), TextInput::make('author')->required(), TextInput::make('license')->required(), Textarea::make('description'), Textarea::make('compatibility')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('version'), TextColumn::make('security_status')->badge(), TextColumn::make('status')->badge(), TextColumn::make('ratings_count')->counts('ratings')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListMarketplaceThemes::route('/')];
    }
}
