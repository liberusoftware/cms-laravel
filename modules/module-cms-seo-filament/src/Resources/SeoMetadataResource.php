<?php

declare(strict_types=1);

namespace Liberu\Cms\SeoFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Seo\Models\SeoMetadata;

final class SeoMetadataResource extends Resource
{
    #[\Override]
    protected static ?string $model = SeoMetadata::class;

    #[\Override]
    protected static ?string $slug = 'cms-seo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('title')->maxLength(60), Textarea::make('description')->maxLength(160), TextInput::make('canonical_url')->url(), TextInput::make('robots')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('seoable_type'), TextColumn::make('seoable_id'), TextColumn::make('title'), TextColumn::make('canonical_url')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListSeoMetadata::route('/')];
    }
}
