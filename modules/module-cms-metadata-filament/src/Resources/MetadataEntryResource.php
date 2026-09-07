<?php

declare(strict_types=1);

namespace Liberu\Cms\MetadataFilament\Resources;

use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Metadata\Models\MetadataEntry;

final class MetadataEntryResource extends Resource
{
    #[\Override]
    protected static ?string $model = MetadataEntry::class;

    #[\Override]
    protected static ?string $slug = 'cms-metadata';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject_type'), TextColumn::make('subject_id'), TextColumn::make('key'), TextColumn::make('value_type')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListMetadataEntries::route('/')];
    }
}
