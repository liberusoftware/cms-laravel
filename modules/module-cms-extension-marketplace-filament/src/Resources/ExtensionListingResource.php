<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplaceFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionListing;

final class ExtensionListingResource extends Resource
{
    protected static ?string $model = ExtensionListing::class;

    protected static ?string $slug = 'cms-extension-marketplace';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required(), TextInput::make('name')->required(), TextInput::make('publisher_id')->numeric()->required(), TextInput::make('category_id')->numeric(), TextInput::make('license')->required(), Textarea::make('description'), Textarea::make('metadata')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('publisher.name')->label('Publisher'), TextColumn::make('status')->badge(), TextColumn::make('security_status')->badge(), TextColumn::make('reviews_count')->counts('reviews')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListExtensionListings::route('/')];
    }
}
